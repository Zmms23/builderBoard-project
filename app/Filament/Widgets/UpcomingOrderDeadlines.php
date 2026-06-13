<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Helpers\Price;
use App\Models\Company;
use App\Models\Order;
use App\Settings\CompanySettings;
use App\Support\TenantWorkScope;
use Carbon\CarbonInterface;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingOrderDeadlines extends TableWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('dashboard.deadlines.heading'))
            ->description(__('dashboard.deadlines.description'))
            ->query(fn (): Builder => $this->ordersQuery())
            ->columns([
                TextColumn::make('number')
                    ->label(__('dashboard.deadlines.columns.number'))
                    ->searchable(),
                TextColumn::make('title')
                    ->label(__('dashboard.deadlines.columns.title'))
                    ->searchable()
                    ->limit(32),
                TextColumn::make('project.title')
                    ->label(__('dashboard.deadlines.columns.project'))
                    ->limit(28),
                TextColumn::make('client.name')
                    ->label(__('dashboard.deadlines.columns.client'))
                    ->limit(28),
                TextColumn::make('status')
                    ->label(__('dashboard.deadlines.columns.status'))
                    ->formatStateUsing(fn (OrderStatus|string|null $state): string => $this->formatStatus($state))
                    ->badge()
                    ->color(fn (OrderStatus|string|null $state): string => $this->statusColor($state)),
                TextColumn::make('deadline')
                    ->label(__('dashboard.deadlines.columns.deadline'))
                    ->date()
                    ->sortable()
                    ->badge()
                    ->color(fn (CarbonInterface|string|null $state): string => $state instanceof CarbonInterface && $state->isPast() ? 'danger' : 'gray'),
                TextColumn::make('estimated_price_amount')
                    ->label(__('dashboard.deadlines.columns.estimated_price'))
                    ->formatStateUsing(fn (int|float|string|null $state): string => Price::format($state, $this->currency()))
                    ->sortable(),
                TextColumn::make('progress')
                    ->label(__('dashboard.deadlines.columns.progress'))
                    ->suffix('%')
                    ->visible(fn (): bool => app(CompanySettings::class)->client_progress_enabled)
                    ->sortable(),
            ])
            ->defaultSort('deadline')
            ->paginated(false)
            ->emptyStateIcon(Heroicon::CheckCircle)
            ->emptyStateHeading(__('dashboard.deadlines.empty_heading'))
            ->emptyStateDescription(__('dashboard.deadlines.empty_description'));
    }

    public static function canView(): bool
    {
        $user = Filament::auth()->user();
        $tenant = Filament::getTenant();

        return $tenant instanceof Company
            && $user?->canAccessTenant($tenant) === true;
    }

    private function ordersQuery(): Builder
    {
        $tenant = Filament::getTenant();
        $query = Order::query()
            ->with(['client', 'project']);

        if (! $tenant instanceof Company) {
            return $query->whereRaw('1 = 0');
        }

        return TenantWorkScope::orders($query)
            ->where('company_id', $tenant->getKey())
            ->whereNotNull('deadline')
            ->whereNotIn('status', [
                OrderStatus::Approved->value,
                OrderStatus::Rejected->value,
            ])
            ->limit(6);
    }

    private function formatStatus(OrderStatus|string|null $state): string
    {
        return $state instanceof OrderStatus
            ? $state->getLabel()
            : OrderStatus::tryFrom((string) $state)?->getLabel() ?? '-';
    }

    private function statusColor(OrderStatus|string|null $state): string
    {
        $status = $state instanceof OrderStatus
            ? $state
            : OrderStatus::tryFrom((string) $state);

        return match ($status) {
            OrderStatus::Draft => 'gray',
            OrderStatus::Pending => 'warning',
            OrderStatus::Approved => 'success',
            OrderStatus::Rejected => 'danger',
            default => 'gray',
        };
    }

    private function currency(): string
    {
        return app(CompanySettings::class)->currency->value;
    }
}
