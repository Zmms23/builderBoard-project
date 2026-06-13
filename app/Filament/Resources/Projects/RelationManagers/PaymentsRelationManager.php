<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Helpers\Price;
use App\Models\Order;
use App\Settings\CompanySettings;
use App\Support\TenantWorkScope;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Payments';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return app(CompanySettings::class)->budget_tracking_enabled
            && parent::canViewForRecord($ownerRecord, $pageClass);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->label(__('payment.fields.order'))
                    ->options(fn (): array => $this->orderOptions())
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function (Set $set, int|string|null $state): void {
                        $order = $this->findOrder($state);

                        if (! $order) {
                            $set('client_id', null);
                            $set('amount', Price::fromAmount(0));

                            return;
                        }

                        $set('client_id', $order->client_id);
                        $set('amount', Price::fromAmount($this->remainingAmount($order)));
                    })
                    ->required(),
                Select::make('client_id')
                    ->label(__('payment.fields.client'))
                    ->options(fn (): array => $this->clientOptions())
                    ->disabled()
                    ->dehydrated()
                    ->native(false)
                    ->required(),
                TextInput::make('amount')
                    ->label(__('payment.fields.amount'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->prefix(fn (): string => $this->currency())
                    ->formatStateUsing(fn (int|float|string|null $state): string => Price::fromAmount($state))
                    ->dehydrateStateUsing(fn (int|float|string|null $state): int => Price::toAmount($state))
                    ->required(),
                Select::make('status')
                    ->label(__('payment.fields.status'))
                    ->options(PaymentStatus::class)
                    ->default(PaymentStatus::Pending)
                    ->native(false)
                    ->required(),
                DatePicker::make('paid_at')
                    ->label(__('payment.fields.paid_at'))
                    ->native(false),
                Select::make('method')
                    ->label(__('payment.fields.method'))
                    ->options(fn (): array => $this->methodOptions())
                    ->default(fn (): string => array_key_first($this->methodOptions()) ?? PaymentMethod::Other->value)
                    ->native(false)
                    ->required(),
                TextInput::make('reference')
                    ->label(__('payment.fields.reference'))
                    ->maxLength(255),
                Textarea::make('notes')
                    ->label(__('payment.fields.notes'))
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('reference')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('order.number')
                    ->label(__('payment.columns.order'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('client.name')
                    ->label(__('payment.columns.client'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label(__('payment.columns.amount'))
                    ->formatStateUsing(fn (int|float|string|null $state): string => Price::format($state, $this->currency()))
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('payment.columns.status'))
                    ->formatStateUsing(fn (PaymentStatus|string|null $state): string => $this->formatStatus($state))
                    ->badge()
                    ->color(fn (PaymentStatus|string|null $state): string => $this->statusColor($state)),
                TextColumn::make('method')
                    ->label(__('payment.columns.method'))
                    ->formatStateUsing(fn (?string $state): string => $this->formatMethod($state))
                    ->badge()
                    ->color(fn (?string $state): string => PaymentMethod::tryFrom((string) $state)?->getColor() ?? 'gray')
                    ->searchable(),
                TextColumn::make('paid_at')
                    ->label(__('payment.columns.paid_at'))
                    ->date()
                    ->sortable(),
                TextColumn::make('reference')
                    ->label(__('payment.columns.reference'))
                    ->placeholder('-')
                    ->searchable()
                    ->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon(Heroicon::Plus)
                    ->mutateDataUsing(fn (array $data): array => $this->paymentDefaults($data)),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateDataUsing(fn (array $data): array => $this->paymentDefaults($data)),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    /**
     * @return array<int, string>
     */
    private function orderOptions(): array
    {
        $project = $this->getOwnerRecord();

        return TenantWorkScope::orders(Order::query())
            ->with('client')
            ->where('company_id', $project->company_id)
            ->where('project_id', $project->getKey())
            ->orderBy('number')
            ->get()
            ->mapWithKeys(fn (Order $order): array => [
                $order->id => "{$order->number} - {$order->title} ({$order->client?->name})",
            ])
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function clientOptions(): array
    {
        $project = $this->getOwnerRecord()->loadMissing('client');

        if (! $project->client) {
            return [];
        }

        return [
            $project->client->id => $project->client->name,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function methodOptions(): array
    {
        $settings = app(CompanySettings::class);
        $options = [
            PaymentMethod::Card->value => PaymentMethod::Card->getLabel(),
            PaymentMethod::Other->value => PaymentMethod::Other->getLabel(),
        ];

        if ($settings->cash_payments_enabled) {
            $options = [
                PaymentMethod::Cash->value => PaymentMethod::Cash->getLabel(),
                ...$options,
            ];
        }

        if ($settings->bank_transfer_enabled) {
            $options = [
                PaymentMethod::BankTransfer->value => PaymentMethod::BankTransfer->getLabel(),
                ...$options,
            ];
        }

        return $options;
    }

    private function findOrder(int|string|null $id): ?Order
    {
        if (blank($id)) {
            return null;
        }

        $project = $this->getOwnerRecord();

        return TenantWorkScope::orders(Order::query())
            ->with(['client', 'payments'])
            ->where('company_id', $project->company_id)
            ->where('project_id', $project->getKey())
            ->whereKey($id)
            ->first();
    }

    private function remainingAmount(Order $order): int
    {
        $paidAmount = $order->payments
            ->where('status', PaymentStatus::Paid)
            ->sum('amount');

        return max(0, $order->estimated_price_amount - $paidAmount);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function paymentDefaults(array $data): array
    {
        $project = $this->getOwnerRecord();
        $order = $this->findOrder($data['order_id'] ?? null);

        return [
            ...$data,
            'company_id' => $project->company_id,
            'project_id' => $project->getKey(),
            'client_id' => $order?->client_id ?? $project->client_id,
        ];
    }

    private function currency(): string
    {
        return app(CompanySettings::class)->currency->value;
    }

    private function formatStatus(PaymentStatus|string|null $state): string
    {
        return $state instanceof PaymentStatus
            ? $state->getLabel()
            : PaymentStatus::tryFrom((string) $state)?->getLabel() ?? '-';
    }

    private function statusColor(PaymentStatus|string|null $state): string
    {
        $status = $state instanceof PaymentStatus
            ? $state
            : PaymentStatus::tryFrom((string) $state);

        return match ($status) {
            PaymentStatus::Pending => 'warning',
            PaymentStatus::Paid => 'success',
            PaymentStatus::Failed => 'danger',
            PaymentStatus::Refunded => 'gray',
            default => 'gray',
        };
    }

    private function formatMethod(?string $state): string
    {
        $method = PaymentMethod::tryFrom((string) $state);

        if ($method) {
            return $method->getLabel();
        }

        return filled($state)
            ? str($state)->replace('_', ' ')->headline()->toString()
            : '-';
    }
}
