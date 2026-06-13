<?php

namespace App\Filament\Resources;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Filament\Resources\PaymentResource\Pages\CreatePayment;
use App\Filament\Resources\PaymentResource\Pages\EditPayment;
use App\Filament\Resources\PaymentResource\Pages\ListPayments;
use App\Helpers\Price;
use App\Models\Client;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Project;
use App\Settings\CompanySettings;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $recordTitleAttribute = 'reference';

    protected static ?string $tenantOwnershipRelationshipName = 'company';

    protected static ?int $navigationSort = 55;

    public static function canViewAny(): bool
    {
        return app(CompanySettings::class)->budget_tracking_enabled
            && parent::canViewAny();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('payment.sections.details'))
                    ->schema([
                        Select::make('order_id')
                            ->label(__('payment.fields.order'))
                            ->options(fn (): array => static::orderOptions())
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set, int|string|null $state): void {
                                $order = static::findOrder($state);

                                if (! $order) {
                                    $set('project_id', null);
                                    $set('client_id', null);
                                    $set('amount', Price::fromAmount(0));

                                    return;
                                }

                                $set('project_id', $order->project?->id);
                                $set('client_id', $order->client_id);
                                $set('amount', Price::fromAmount(static::remainingAmount($order)));
                            })
                            ->required(),
                        Select::make('project_id')
                            ->label(__('payment.fields.project'))
                            ->options(fn (): array => static::projectOptions())
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->dehydrated()
                            ->native(false),
                        Select::make('client_id')
                            ->label(__('payment.fields.client'))
                            ->options(fn (): array => static::clientOptions())
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->dehydrated()
                            ->native(false)
                            ->required(),
                        TextInput::make('amount')
                            ->label(__('payment.fields.amount'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->prefix(fn (): string => static::currency())
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
                            ->options(fn (): array => static::methodOptions())
                            ->default(fn (): string => array_key_first(static::methodOptions()) ?? PaymentMethod::Other->value)
                            ->native(false)
                            ->required(),
                        TextInput::make('reference')
                            ->label(__('payment.fields.reference'))
                            ->maxLength(255),
                        Textarea::make('notes')
                            ->label(__('payment.fields.notes'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns([
                        'lg' => 2,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('order.number')
                    ->label(__('payment.columns.order'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('project.title')
                    ->label(__('payment.columns.project'))
                    ->limit(28)
                    ->searchable(),
                TextColumn::make('client.name')
                    ->label(__('payment.columns.client'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label(__('payment.columns.amount'))
                    ->formatStateUsing(fn (int|float|string|null $state): string => Price::format($state, static::currency()))
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('payment.columns.status'))
                    ->formatStateUsing(fn (PaymentStatus|string|null $state): string => static::formatStatus($state))
                    ->badge()
                    ->color(fn (PaymentStatus|string|null $state): string => static::statusColor($state)),
                TextColumn::make('paid_at')
                    ->label(__('payment.columns.paid_at'))
                    ->date()
                    ->sortable(),
                TextColumn::make('method')
                    ->label(__('payment.columns.method'))
                    ->formatStateUsing(fn (?string $state): string => static::formatMethod($state))
                    ->badge()
                    ->color(fn (?string $state): string => static::methodColor($state))
                    ->searchable(),
                TextColumn::make('reference')
                    ->label(__('payment.columns.reference'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('payment.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('payment.filters.status'))
                    ->options(PaymentStatus::class),
                SelectFilter::make('client_id')
                    ->label(__('payment.filters.client'))
                    ->options(fn (): array => static::clientOptions()),
                SelectFilter::make('order_id')
                    ->label(__('payment.filters.order'))
                    ->options(fn (): array => static::orderOptions()),
                SelectFilter::make('method')
                    ->label(__('payment.filters.method'))
                    ->options(fn (): array => static::methodOptions()),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
            'create' => CreatePayment::route('/create'),
            'edit' => EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('payment.navigation.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('payment.navigation.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('payment.navigation.plural');
    }

    public static function getNavigationBadge(): ?string
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return null;
        }

        return (string) Payment::query()
            ->where('company_id', $tenant->getKey())
            ->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('payment.navigation.badge');
    }

    /**
     * @return array<int, string>
     */
    private static function orderOptions(): array
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return [];
        }

        return Order::query()
            ->with(['client', 'project'])
            ->where('company_id', $tenant->getKey())
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
    private static function projectOptions(): array
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return [];
        }

        return Project::query()
            ->where('company_id', $tenant->getKey())
            ->orderBy('title')
            ->pluck('title', 'id')
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private static function clientOptions(): array
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return [];
        }

        return Client::query()
            ->where('company_id', $tenant->getKey())
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private static function methodOptions(): array
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

    private static function findOrder(int|string|null $id): ?Order
    {
        if (blank($id)) {
            return null;
        }

        $tenant = Filament::getTenant();

        if (! $tenant) {
            return null;
        }

        return Order::query()
            ->with(['client', 'project', 'payments'])
            ->where('company_id', $tenant->getKey())
            ->whereKey($id)
            ->first();
    }

    private static function remainingAmount(Order $order): int
    {
        $paidAmount = $order->payments
            ->where('status', PaymentStatus::Paid)
            ->sum('amount');

        return max(0, $order->estimated_price_amount - $paidAmount);
    }

    private static function currency(): string
    {
        return app(CompanySettings::class)->currency->value;
    }

    private static function formatStatus(PaymentStatus|string|null $state): string
    {
        return $state instanceof PaymentStatus
            ? $state->getLabel()
            : PaymentStatus::tryFrom((string) $state)?->getLabel() ?? '-';
    }

    private static function statusColor(PaymentStatus|string|null $state): string
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

    private static function formatMethod(?string $state): string
    {
        $method = PaymentMethod::tryFrom((string) $state);

        if ($method) {
            return $method->getLabel();
        }

        return filled($state)
            ? str($state)->replace('_', ' ')->headline()->toString()
            : '-';
    }

    private static function methodColor(?string $state): string
    {
        return PaymentMethod::tryFrom((string) $state)?->getColor() ?? 'gray';
    }
}
