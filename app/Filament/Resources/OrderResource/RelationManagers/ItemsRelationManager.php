<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Helpers\Price;
use App\Models\Subservice;
use App\Settings\CompanySettings;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('subservice_id')
                    ->label(__('order_item.fields.subservice'))
                    ->options(fn (): array => $this->subserviceOptions())
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set, mixed $state): void {
                        $subservice = Subservice::find($state);

                        if (! $subservice) {
                            return;
                        }

                        $quantity = $get('quantity') ?: 1;

                        $set('description', $subservice->description);
                        $unitPrice = Price::fromAmount($subservice->price_amount);

                        $set('unit_price_amount', $unitPrice);
                        $set('total_price_amount', $this->calculateTotal($quantity, $unitPrice));
                    }),
                Textarea::make('description')
                    ->label(__('order_item.fields.description'))
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('quantity')
                    ->label(__('order_item.fields.quantity'))
                    ->numeric()
                    ->default(1)
                    ->minValue(0.01)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set): void {
                        $set('total_price_amount', $this->calculateTotal($get('quantity'), $get('unit_price_amount')));
                    })
                    ->required(),
                TextInput::make('unit_price_amount')
                    ->label(__('order_item.fields.unit_price'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->prefix(fn (): string => $this->currency())
                    ->formatStateUsing(fn (int | float | string | null $state): string => Price::fromAmount($state))
                    ->dehydrateStateUsing(fn (int | float | string | null $state): int => Price::toAmount($state))
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set): void {
                        $set('total_price_amount', $this->calculateTotal($get('quantity'), $get('unit_price_amount')));
                    })
                    ->required(),
                TextInput::make('total_price_amount')
                    ->label(__('order_item.fields.total_price'))
                    ->numeric()
                    ->default(0)
                    ->prefix(fn (): string => $this->currency())
                    ->formatStateUsing(fn (int | float | string | null $state): string => Price::fromAmount($state))
                    ->dehydrateStateUsing(fn (int | float | string | null $state): int => Price::toAmount($state))
                    ->disabled()
                    ->dehydrated()
                    ->required(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('subservice.name')
            ->columns([
                TextColumn::make('subservice.service.name')
                    ->label(__('order_item.columns.service'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('subservice.name')
                    ->label(__('order_item.columns.subservice'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('order_item.columns.description'))
                    ->limit(40)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('quantity')
                    ->label(__('order_item.columns.quantity'))
                    ->sortable(),
                TextColumn::make('unit_price_amount')
                    ->label(__('order_item.columns.unit_price'))
                    ->formatStateUsing(fn (int | float | string | null $state): string => Price::format($state, $this->currency()))
                    ->sortable(),
                TextColumn::make('total_price_amount')
                    ->label(__('order_item.columns.total_price'))
                    ->formatStateUsing(fn (int | float | string | null $state): string => Price::format($state, $this->currency()))
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

    /**
     * @return array<int, string>
     */
    private function subserviceOptions(): array
    {
        $companyId = $this->getOwnerRecord()->company_id;

        return Subservice::query()
            ->with('service')
            ->whereHas('service', fn ($query) => $query
                ->where('company_id', $companyId)
                ->where('is_active', true))
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Subservice $subservice): array => [
                $subservice->id => "{$subservice->service->name} - {$subservice->name}",
            ])
            ->all();
    }

    private function calculateTotal(int | float | string | null $quantity, int | float | string | null $unitPrice): string
    {
        return number_format(((float) ($quantity ?: 0)) * ((float) ($unitPrice ?: 0)), 2, '.', '');
    }

    private function currency(): string
    {
        return app(CompanySettings::class)->currency->value;
    }
}
