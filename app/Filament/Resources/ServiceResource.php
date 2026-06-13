<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages\CreateService;
use App\Filament\Resources\ServiceResource\Pages\EditService;
use App\Filament\Resources\ServiceResource\Pages\ListServices;
use App\Filament\Resources\ServiceResource\RelationManagers\SubservicesRelationManager;
use App\Helpers\Price;
use App\Models\Service;
use App\Settings\CompanySettings;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $tenantOwnershipRelationshipName = 'company';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('service.sections.details'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('service.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->scopedUnique(ignoreRecord: true),
                        Textarea::make('description')
                            ->label(__('service.fields.description'))
                            ->rows(4)
                            ->columnSpanFull(),
                        TextInput::make('base_price_amount')
                            ->label(__('service.fields.base_price'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->prefix(fn (): string => static::currency())
                            ->formatStateUsing(fn (int|float|string|null $state): string => Price::fromAmount($state))
                            ->dehydrateStateUsing(fn (int|float|string|null $state): int => Price::toAmount($state))
                            ->required(),
                        Toggle::make('is_active')
                            ->label(__('service.fields.is_active'))
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columns([
                        'lg' => 2,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('service.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('service.columns.description'))
                    ->limit(40)
                    ->wrap(),
                TextColumn::make('base_price_amount')
                    ->label(__('service.columns.base_price'))
                    ->formatStateUsing(fn (int|float|string|null $state): string => static::formatMoney($state))
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label(__('service.columns.is_active'))
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('service.columns.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('service.filters.is_active'))
                    ->placeholder(__('service.filters.all'))
                    ->trueLabel(__('service.filters.active'))
                    ->falseLabel(__('service.filters.inactive')),
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
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            SubservicesRelationManager::class,
        ];
    }

    public static function getModelLabel(): string
    {
        return __('service.navigation.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('service.navigation.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('service.navigation.plural');
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
        return __('service.navigation.badge');
    }

    private static function currency(): string
    {
        return app(CompanySettings::class)->currency->value;
    }

    private static function formatMoney(int|float|string|null $state): string
    {
        return Price::format($state, static::currency());
    }
}
