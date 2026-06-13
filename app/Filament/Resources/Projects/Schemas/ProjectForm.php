<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\ClientStatus;
use App\Enums\ClientType;
use App\Enums\ProjectStatus;
use App\Helpers\Price;
use App\Models\Client;
use App\Settings\CompanySettings;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('project.sections.details'))
                    ->schema([
                        Select::make('client_id')
                            ->label(__('project.fields.client'))
                            ->options(fn (): array => static::clientOptions())
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label(__('client.fields.name'))
                                    ->required()
                                    ->maxLength(255),
                                Select::make('type')
                                    ->label(__('client.fields.type'))
                                    ->options(ClientType::class)
                                    ->default(ClientType::Person)
                                    ->native(false)
                                    ->required(),
                                Select::make('status')
                                    ->label(__('client.fields.status'))
                                    ->options(ClientStatus::class)
                                    ->default(ClientStatus::Active)
                                    ->native(false)
                                    ->required(),
                                TextInput::make('phone')
                                    ->label(__('client.fields.phone'))
                                    ->tel()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->label(__('client.fields.email'))
                                    ->email()
                                    ->maxLength(255),
                                Textarea::make('address')
                                    ->label(__('client.fields.address'))
                                    ->rows(3)
                                    ->columnSpanFull(),
                                Textarea::make('notes')
                                    ->label(__('client.fields.notes'))
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->createOptionUsing(fn (array $data): int => static::createClient($data))
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
                            ->formatStateUsing(fn (int|float|string|null $state): string => Price::fromAmount($state))
                            ->dehydrateStateUsing(fn (int|float|string|null $state): int => Price::toAmount($state))
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

    /**
     * @param  array<string, mixed>  $data
     */
    private static function createClient(array $data): int
    {
        $tenant = Filament::getTenant();

        $client = Client::query()->create([
            ...$data,
            'company_id' => $tenant?->getKey(),
        ]);

        return (int) $client->getKey();
    }
}
