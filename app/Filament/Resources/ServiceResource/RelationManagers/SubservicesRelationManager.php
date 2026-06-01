<?php

namespace App\Filament\Resources\ServiceResource\RelationManagers;

use App\Enums\PricingType;
use App\Enums\UnitType;
use App\Settings\CompanySettings;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class SubservicesRelationManager extends RelationManager
{
    protected static string $relationship = 'subservices';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('subservice.fields.name'))
                    ->required()
                    ->maxLength(255)
                    ->scopedUnique(
                        ignoreRecord: true,
                        modifyQueryUsing: fn ($query) => $query
                            ->where('service_id', $this->getOwnerRecord()->getKey()),
                    ),

                Textarea::make('description')
                    ->label(__('subservice.fields.description'))
                    ->rows(4)
                    ->columnSpanFull(),

                TextInput::make('price')
                    ->label(__('subservice.fields.price'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->prefix(fn (): string => $this->currency())
                    ->required(),

                Select::make('pricing_type')
                    ->label(__('subservice.fields.pricing_type'))
                    ->options(PricingType::class)
                    ->default(PricingType::Fixed)
                    ->native(false)
                    ->required(),

                Select::make('unit')
                    ->label(__('subservice.fields.unit'))
                    ->options(UnitType::class)
                    ->default(UnitType::Service)
                    ->native(false)
                    ->required(),

                TextInput::make('estimated_duration')
                    ->label(__('subservice.fields.estimated_duration'))
                    ->maxLength(255)
                    ->placeholder(__('subservice.placeholders.estimated_duration'))
                    ->helperText(__('subservice.helpers.estimated_duration')),

                Toggle::make('is_active')
                    ->label(__('subservice.fields.is_active'))
                    ->default(true),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('subservice.columns.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label(__('subservice.columns.description'))
                    ->limit(40)
                    ->wrap()
                    ->placeholder('-'),

                TextColumn::make('price')
                    ->label(__('subservice.columns.price'))
                    ->formatStateUsing(fn (int | float | string | null $state): string => $this->formatMoney($state))
                    ->sortable(),

                TextColumn::make('pricing_type')
                    ->label(__('subservice.columns.pricing_type'))
                    ->formatStateUsing(fn (PricingType | string | null $state): string => $this->formatPricingType($state))
                    ->badge(),

                TextColumn::make('unit')
                    ->label(__('subservice.columns.unit'))
                    ->formatStateUsing(fn (UnitType | string | null $state): string => $this->formatUnit($state))
                    ->badge(),

                TextColumn::make('estimated_duration')
                    ->label(__('subservice.columns.estimated_duration'))
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                ToggleColumn::make('is_active')
                    ->label(__('subservice.columns.is_active'))
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    private function currency(): string
    {
        return app(CompanySettings::class)->currency->value;
    }

    private function formatMoney(int | float | string | null $state): string
    {
        if ($state === null || $state === '') {
            return '0.00 ' . $this->currency();
        }

        return number_format((float) $state, 2) . ' ' . $this->currency();
    }

    private function formatPricingType(PricingType | string | null $state): string
    {
        return $state instanceof PricingType
            ? $state->getLabel()
            : PricingType::tryFrom((string) $state)?->getLabel() ?? '-';
    }

    private function formatUnit(UnitType | string | null $state): string
    {
        return $state instanceof UnitType
            ? $state->getLabel()
            : UnitType::tryFrom((string) $state)?->getLabel() ?? '-';
    }
}