<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages\CreateService;
use App\Filament\Resources\ServiceResource\Pages\EditService;
use App\Filament\Resources\ServiceResource\Pages\ListServices;
use App\Models\Service;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $tenantOwnershipRelationshipName = 'company';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        Section::make(__('service.sections.details'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('service.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state): mixed => $set('slug', Str::slug($state ?? ''))),
                                TextInput::make('slug')
                                    ->label(__('service.fields.slug'))
                                    ->required()
                                    ->maxLength(255)
                                    ->alphaDash()
                                    ->scopedUnique(ignoreRecord: true),
                                Textarea::make('description')
                                    ->label(__('service.fields.description'))
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpan([
                                'lg' => 2,
                            ]),
                        Section::make(__('service.sections.pricing'))
                            ->schema([
                                TextInput::make('base_price')
                                    ->label(__('service.fields.base_price'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->required(),
                                Toggle::make('is_active')
                                    ->label(__('service.fields.is_active'))
                                    ->default(true)
                                    ->inline(false),
                            ])
                            ->columnSpan([
                                'lg' => 1,
                            ]),
                    ])
                    ->columns([
                        'lg' => 3,
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
                TextColumn::make('slug')
                    ->label(__('service.columns.slug'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('description')
                    ->label(__('service.columns.description'))
                    ->limit(40)
                    ->wrap(),
                TextColumn::make('base_price')
                    ->label(__('service.columns.base_price'))
                    ->formatStateUsing(fn (mixed $state): string => number_format((float) $state, 2))
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('service.columns.is_active'))
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label(__('service.columns.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
}
