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
use App\Models\Project;
use App\Models\User;
use App\Settings\CompanySettings;
use App\Support\TenantWorkScope;
use BackedEnum;
use Filament\Actions\Action;
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
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'number';

    protected static ?string $tenantOwnershipRelationshipName = 'company';

    protected static ?int $navigationSort = 40;

    protected static bool $shouldRegisterNavigation = false;

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

                        Select::make('project_id')
                            ->label(__('order.fields.project'))
                            ->options(fn (): array => static::projectOptions())
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set, int|string|null $state): void {
                                $project = Project::query()->find($state);

                                $set('client_id', $project?->client_id);
                            })
                            ->required(),

                        TextInput::make('title')
                            ->label(__('order.fields.title'))
                            ->required()
                            ->maxLength(255),

                        Select::make('client_id')
                            ->label(__('order.fields.client'))
                            ->options(fn (): array => static::clientOptions())
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->dehydrated()
                            ->native(false)
                            ->required(),

                        Select::make('assigned_user_id')
                            ->label(__('order.fields.assigned_user'))
                            ->helperText(__('order.help.assigned_user'))
                            ->options(fn (): array => static::memberOptions())
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Select::make('status')
                            ->label(__('order.fields.status'))
                            ->options(OrderStatus::class)
                            ->default(OrderStatus::Draft)
                            ->native(false)
                            ->required(),

                        DatePicker::make('deadline')
                            ->label(__('order.fields.deadline'))
                            ->native(false),

                        TextInput::make('progress')
                            ->label(__('order.fields.progress'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0)
                            ->suffix('%')
                            ->required(),

                        TextInput::make('estimated_price_amount')
                            ->label(__('order.fields.estimated_price'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->prefix(fn (): string => static::currency())
                            ->formatStateUsing(fn (int|float|string|null $state): string => Price::fromAmount($state))
                            ->dehydrateStateUsing(fn (int|float|string|null $state): int => Price::toAmount($state))
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

                TextColumn::make('project.title')
                    ->label(__('order.columns.project'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('client.name')
                    ->label(__('order.columns.client'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('assignedUser.name')
                    ->label(__('order.columns.assigned_user'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('order.columns.status'))
                    ->formatStateUsing(fn (OrderStatus|string|null $state): string => static::formatOrderStatus($state))
                    ->badge()
                    ->color(fn (OrderStatus|string|null $state): string => static::orderStatusColor($state)),

                TextColumn::make('estimated_price_amount')
                    ->label(__('order.columns.estimated_price'))
                    ->formatStateUsing(fn (int|float|string|null $state): string => static::formatMoney($state))
                    ->sortable(),

                TextColumn::make('deadline')
                    ->label(__('order.columns.deadline'))
                    ->date()
                    ->sortable(),

                TextColumn::make('progress')
                    ->label(__('order.columns.progress'))
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('order.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('project_id')
                    ->label(__('order.filters.project'))
                    ->options(fn (): array => static::projectOptions()),

                SelectFilter::make('client_id')
                    ->label(__('order.filters.client'))
                    ->options(fn (): array => static::clientOptions()),

                SelectFilter::make('assigned_user_id')
                    ->label(__('order.filters.assigned_user'))
                    ->options(fn (): array => static::memberOptions()),

                SelectFilter::make('status')
                    ->label(__('order.filters.status'))
                    ->options(OrderStatus::class),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label(__('order.actions.approve.label'))
                    ->icon(Heroicon::CheckBadge)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record): bool => static::canManageStatus($record)
                        && static::hasStatus($record, [
                            OrderStatus::Draft,
                            OrderStatus::Pending,
                        ]))
                    ->action(function (Order $record): void {
                        $userId = Filament::auth()->id();

                        $isAssignedWorker = $record->assigned_user_id !== null
                            && (string) $record->assigned_user_id === (string) $userId;

                        $record->update([
                            'status' => $isAssignedWorker
                                ? OrderStatus::Approved
                                : OrderStatus::Pending,
                        ]);
                    })
                    ->successNotificationTitle(__('order.actions.approve.success')),

                Action::make('reject')
                    ->label(__('order.actions.reject.label'))
                    ->icon(Heroicon::XMark)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record): bool => static::canManageStatus($record)
                        && static::hasStatus($record, [
                            OrderStatus::Draft,
                            OrderStatus::Pending,
                        ]))
                    ->action(fn (Order $record): bool => $record->update([
                        'status' => OrderStatus::Rejected,
                    ]))
                    ->successNotificationTitle(__('order.actions.reject.success')),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return TenantWorkScope::orders(
            parent::getEloquentQuery()->with(['project', 'client', 'assignedUser'])
        );
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
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
        return Filament::getTenant()
            ? (string) static::getEloquentQuery()->count()
            : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('order.navigation.badge');
    }

    private static function projectOptions(): array
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return [];
        }

        return Project::query()
            ->with('client')
            ->where('company_id', $tenant->getKey())
            ->orderBy('title')
            ->get()
            ->mapWithKeys(fn (Project $project): array => [
                $project->id => $project->client
                    ? "{$project->title} - {$project->client->name}"
                    : $project->title,
            ])
            ->all();
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

    private static function memberOptions(): array
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return [];
        }

        return User::query()
            ->whereHas('companies', fn (Builder $query): Builder => $query->whereKey($tenant->getKey()))
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (User $user): array => [
                $user->id => "{$user->name} ({$user->email})",
            ])
            ->all();
    }

    private static function nextOrderNumber(): string
    {
        $tenant = Filament::getTenant();

        $nextNumber = Order::query()
            ->when($tenant, fn (Builder $query): Builder => $query->where('company_id', $tenant->getKey()))
            ->count() + 1;

        return 'ORD-'.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private static function currency(): string
    {
        $currency = app(CompanySettings::class)->currency ?? 'GEL';

        return $currency instanceof UnitEnum
            ? $currency->value
            : (string) $currency;
    }

    private static function formatMoney(int|float|string|null $state): string
    {
        return Price::format($state, static::currency());
    }

    private static function formatOrderStatus(OrderStatus|string|null $state): string
    {
        return $state instanceof OrderStatus
            ? $state->getLabel()
            : OrderStatus::tryFrom((string) $state)?->getLabel() ?? '-';
    }

    private static function orderStatusColor(OrderStatus|string|null $state): string
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

    public static function canManageStatus(Order $record): bool
    {
        $user = Filament::auth()->user();

        if (! $user) {
            return false;
        }

        if (static::hasStatus($record, [OrderStatus::Approved, OrderStatus::Rejected])) {
            return false;
        }

        $isAssignedWorker = $record->assigned_user_id !== null
            && (string) $record->assigned_user_id === (string) $user->getKey();

        return $isAssignedWorker || $user->can('Update:Order') === true;
    }

    public static function hasStatus(Order $record, array $statuses): bool
    {
        foreach ($statuses as $status) {
            if ($record->status === $status || $record->status === $status->value) {
                return true;
            }
        }

        return false;
    }
}
