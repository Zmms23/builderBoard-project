<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\OrderStatus;
use App\Enums\ProjectStatus;
use App\Helpers\Price;
use App\Models\Client;
use App\Models\Order;
use App\Models\Project;
use App\Settings\CompanySettings;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('project.sections.details'))
                    ->schema([
                        Select::make('order_id')
                            ->label(__('project.fields.order'))
                            ->options(fn (?Project $record = null): array => static::orderOptions($record))
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->unique(ignoreRecord: true)
                            ->afterStateUpdated(function (Set $set, int | string | null $state): void {
                                $order = Order::query()
                                    ->with('client')
                                    ->find($state);

                                if (! $order) {
                                    return;
                                }

                                $set('client_id', $order->client_id);
                                $set('title', $order->title);
                                $set('budget_amount', Price::fromAmount($order->estimated_price_amount));
                            })
                            ->required(),
                        Select::make('client_id')
                            ->label(__('project.fields.client'))
                            ->options(fn (): array => static::clientOptions())
                            ->disabled()
                            ->dehydrated()
                            ->native(false)
                            ->required(),
                        TextInput::make('title')
                            ->label(__('project.fields.title'))
                            ->required()
                            ->maxLength(255),
                        Select::make('status')
                            ->label(__('project.fields.status'))
                            ->options(ProjectStatus::class)
                            ->default(ProjectStatus::Planning)
                            ->native(false)
                            ->required(),
                        DatePicker::make('deadline')
                            ->label(__('project.fields.deadline'))
                            ->native(false),
                        TextInput::make('progress')
                            ->label(__('project.fields.progress'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0)
                            ->suffix('%')
                            ->required(),
                        TextInput::make('budget_amount')
                            ->label(__('project.fields.budget'))
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->prefix(fn (): string => static::currency())
                            ->formatStateUsing(fn (int | float | string | null $state): string => Price::fromAmount($state))
                            ->dehydrateStateUsing(fn (int | float | string | null $state): int => Price::toAmount($state))
                            ->required(),
                        Textarea::make('notes')
                            ->label(__('project.fields.notes'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns([
                        'lg' => 2,
                    ]),
            ]);
    }

    /**
     * @return array<int, string>
     */
    private static function orderOptions(?Project $record = null): array
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return [];
        }

        return Order::query()
            ->where('company_id', $tenant->getKey())
            ->where('status', OrderStatus::Approved->value)
            ->where(function (Builder $query) use ($record): void {
                $query->whereDoesntHave('project');

                if ($record?->order_id) {
                    $query->orWhere('id', $record->order_id);
                }
            })
            ->orderBy('number')
            ->get()
            ->mapWithKeys(fn (Order $order): array => [
                $order->id => "{$order->number} - {$order->title}",
            ])
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

    private static function currency(): string
    {
        return app(CompanySettings::class)->currency->value;
    }
}
