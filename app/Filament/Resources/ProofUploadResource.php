<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProofUploadResource\Pages\CreateProofUpload;
use App\Filament\Resources\ProofUploadResource\Pages\EditProofUpload;
use App\Filament\Resources\ProofUploadResource\Pages\ListProofUploads;
use App\Helpers\Price;
use App\Models\Order;
use App\Models\Project;
use App\Models\ProofUpload;
use App\Models\User;
use App\Settings\CompanySettings;
use App\Support\TenantWorkScope;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProofUploadResource extends Resource
{
    protected static ?string $model = ProofUpload::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $tenantOwnershipRelationshipName = 'company';

    protected static ?int $navigationSort = 52;

    protected static bool $shouldRegisterNavigation = false;

    public static function canViewAny(): bool
    {
        return app(CompanySettings::class)->proof_upload_enabled
            && parent::canViewAny();
    }

    public static function canCreate(): bool
    {
        return app(CompanySettings::class)->proof_upload_enabled
            && parent::canCreate();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('proof_upload.sections.details'))
                    ->schema([
                        Select::make('order_id')
                            ->label(__('proof_upload.fields.order'))
                            ->options(fn (): array => static::orderOptions())
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set, int|string|null $state): void {
                                $order = static::findOrder($state);

                                $set('project_id', $order?->project_id);
                            })
                            ->required(),
                        Select::make('project_id')
                            ->label(__('proof_upload.fields.project'))
                            ->options(fn (): array => static::projectOptions())
                            ->disabled()
                            ->dehydrated()
                            ->native(false),
                        TextInput::make('title')
                            ->label(__('proof_upload.fields.title'))
                            ->required()
                            ->maxLength(255),
                        FileUpload::make('photo_path')
                            ->label(__('proof_upload.fields.photo'))
                            ->image()
                            ->disk('public')
                            ->directory('proof-uploads')
                            ->visibility('public')
                            ->maxSize(4096)
                            ->openable()
                            ->downloadable()
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('expense_amount')
                            ->label(__('proof_upload.fields.expense'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->prefix(fn (): string => static::currency())
                            ->formatStateUsing(fn (int|float|string|null $state): string => Price::fromAmount($state))
                            ->dehydrateStateUsing(fn (int|float|string|null $state): int => Price::toAmount($state))
                            ->visible(fn (): bool => app(CompanySettings::class)->budget_tracking_enabled),
                        Toggle::make('is_client_visible')
                            ->label(__('proof_upload.fields.is_client_visible'))
                            ->helperText(__('proof_upload.help.is_client_visible'))
                            ->default(fn (): bool => app(CompanySettings::class)->client_progress_enabled)
                            ->visible(fn (): bool => app(CompanySettings::class)->client_progress_enabled),
                        Textarea::make('comment')
                            ->label(__('proof_upload.fields.comment'))
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
                ImageColumn::make('photo_path')
                    ->label(__('proof_upload.columns.photo'))
                    ->disk('public')
                    ->square(),
                TextColumn::make('title')
                    ->label(__('proof_upload.columns.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order.number')
                    ->label(__('proof_upload.columns.order'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('project.title')
                    ->label(__('proof_upload.columns.project'))
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('uploadedBy.name')
                    ->label(__('proof_upload.columns.uploaded_by'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('expense_amount')
                    ->label(__('proof_upload.columns.expense'))
                    ->formatStateUsing(fn (int|float|string|null $state): string => Price::format($state, static::currency()))
                    ->sortable()
                    ->visible(fn (): bool => app(CompanySettings::class)->budget_tracking_enabled),
                TextColumn::make('is_client_visible')
                    ->label(__('proof_upload.columns.is_client_visible'))
                    ->formatStateUsing(fn (?bool $state): string => $state ? __('proof_upload.values.yes') : __('proof_upload.values.no'))
                    ->badge()
                    ->color(fn (?bool $state): string => $state ? 'success' : 'gray')
                    ->visible(fn (): bool => app(CompanySettings::class)->client_progress_enabled),
                TextColumn::make('created_at')
                    ->label(__('proof_upload.columns.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('order_id')
                    ->label(__('proof_upload.filters.order'))
                    ->options(fn (): array => static::orderOptions()),
                SelectFilter::make('uploaded_by_id')
                    ->label(__('proof_upload.filters.uploaded_by'))
                    ->options(fn (): array => static::memberOptions()),
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
            'index' => ListProofUploads::route('/'),
            'create' => CreateProofUpload::route('/create'),
            'edit' => EditProofUpload::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return TenantWorkScope::proofUploads(
            parent::getEloquentQuery()
                ->with(['order', 'project', 'uploadedBy']),
        );
    }

    public static function getModelLabel(): string
    {
        return __('proof_upload.navigation.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('proof_upload.navigation.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('proof_upload.navigation.plural');
    }

    public static function getNavigationBadge(): ?string
    {
        if (! Filament::getTenant() || ! app(CompanySettings::class)->proof_upload_enabled) {
            return null;
        }

        return (string) static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('proof_upload.navigation.badge');
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

        return TenantWorkScope::orders(Order::query())
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

        return TenantWorkScope::projects(Project::query())
            ->where('company_id', $tenant->getKey())
            ->orderBy('title')
            ->pluck('title', 'id')
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private static function memberOptions(): array
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return [];
        }

        return User::query()
            ->whereHas(
                'companies',
                fn (Builder $query): Builder => $query->whereKey($tenant->getKey()),
            )
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public static function findOrder(int|string|null $id): ?Order
    {
        if (blank($id)) {
            return null;
        }

        $tenant = Filament::getTenant();

        if (! $tenant) {
            return null;
        }

        return TenantWorkScope::orders(Order::query())
            ->where('company_id', $tenant->getKey())
            ->whereKey($id)
            ->first();
    }

    private static function currency(): string
    {
        return app(CompanySettings::class)->currency->value;
    }
}
