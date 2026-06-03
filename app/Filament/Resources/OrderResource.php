<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages\CreateOrder;
use App\Filament\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager;
use App\Helpers\Price;
use App\Models\Client;
use App\Models\Order;
use App\Settings\CompanySettings;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'number';

    protected static ?string $tenantOwnershipRelationshipName = 'company';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('order.sections.details'))
                    ->schema([
                        TextInput::make('number')
                            ->label(__('order.fields.number'))
                            ->default(fn (): string => static::nextOrderNumber())
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(255)
                            ->scopedUnique(ignoreRecord: true),
                        TextInput::make('title')
                            ->label(__('order.fields.title'))
                            ->required()
                            ->maxLength(255),
                        Select::make('client_id')
                            ->label(__('order.fields.client'))
                            ->options(fn (): array => static::clientOptions())
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(),
                        Select::make('status')
                            ->label(__('order.fields.status'))
                            ->options(OrderStatus::class)
                            ->default(OrderStatus::Draft)
                            ->native(false)
                            ->required(),
                        TextInput::make('estimated_price_amount')
                            ->label(__('order.fields.estimated_price'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->prefix(fn (): string => static::currency())
                            ->formatStateUsing(fn (int | float | string | null $state): string => Price::fromAmount($state))
                            ->dehydrateStateUsing(fn (int | float | string | null $state): int => Price::toAmount($state))
                            ->required(),
                        Textarea::make('notes')
                            ->label(__('order.fields.notes'))
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
                TextColumn::make('number')
                    ->label(__('order.columns.number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label(__('order.columns.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('client.name')
                    ->label(__('order.columns.client'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('order.columns.status'))
                    ->formatStateUsing(fn (OrderStatus | string | null $state): string => static::formatOrderStatus($state))
                    ->badge()
                    ->color(fn (OrderStatus | string | null $state): string => static::orderStatusColor($state)),
                TextColumn::make('estimated_price_amount')
                    ->label(__('order.columns.estimated_price'))
                    ->formatStateUsing(fn (int | float | string | null $state): string => static::formatMoney($state))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('order.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('client_id')
                    ->label(__('order.filters.client'))
                    ->options(fn (): array => static::clientOptions()),
                SelectFilter::make('status')
                    ->label(__('order.filters.status'))
                    ->options(OrderStatus::class),
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
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('order.navigation.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('order.navigation.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('order.navigation.plural');
    }

    public static function getNavigationBadge(): ?string
    {
        if (! Filament::getTenant()) {
            return null;
        }

        return (string) static::getModel()::query()->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('order.navigation.badge');
    }

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

    private static function nextOrderNumber(): string
    {
        $tenant = Filament::getTenant();

        $nextNumber = Order::query()
            ->when($tenant, fn ($query) => $query->where('company_id', $tenant->getKey()))
            ->count() + 1;

        return 'ORD-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private static function currency(): string
    {
        return app(CompanySettings::class)->currency->value;
    }

    private static function formatMoney(int | float | string | null $state): string
    {
        return Price::format($state, static::currency());
    }

    private static function formatOrderStatus(OrderStatus | string | null $state): string
    {
        return $state instanceof OrderStatus
            ? $state->getLabel()
            : OrderStatus::tryFrom((string) $state)?->getLabel() ?? '-';
    }

    private static function orderStatusColor(OrderStatus | string | null $state): string
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

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }
}
