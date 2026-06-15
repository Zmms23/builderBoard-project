<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\ClientStatus;
use App\Enums\ClientType;
use App\Enums\ProjectStatus;
use App\Helpers\Price;
use App\Models\Client;
use App\Models\User;
use App\Settings\CompanySettings;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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
                Section::make(__('project.sections.quick_order'))
                    ->description(__('project.sections.quick_order_description'))
                    ->schema([
                        Toggle::make('first_order_enabled')
                            ->label(__('project.quick_order.fields.enabled'))
                            ->helperText(__('project.quick_order.help.enabled'))
                            ->live()
                            ->dehydrated(),
                        TextInput::make('first_order_title')
                            ->label(__('project.quick_order.fields.title'))
                            ->maxLength(255)
                            ->required(fn (Get $get): bool => (bool) $get('first_order_enabled'))
                            ->visible(fn (Get $get): bool => (bool) $get('first_order_enabled')),
                        Select::make('first_order_assigned_user_id')
                            ->label(__('project.quick_order.fields.assigned_user'))
                            ->helperText(__('project.quick_order.help.assigned_user'))
                            ->options(fn (): array => static::memberOptions())
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->visible(fn (Get $get): bool => (bool) $get('first_order_enabled')),
                        DatePicker::make('first_order_deadline')
                            ->label(__('project.quick_order.fields.deadline'))
                            ->native(false)
                            ->visible(fn (Get $get): bool => (bool) $get('first_order_enabled')),
                        TextInput::make('first_order_estimated_price_amount')
                            ->label(__('project.quick_order.fields.estimated_price'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->prefix(fn (): string => static::currency())
                            ->formatStateUsing(fn (int|float|string|null $state): string => Price::fromAmount($state))
                            ->dehydrateStateUsing(fn (int|float|string|null $state): int => Price::toAmount($state))
                            ->visible(fn (Get $get): bool => (bool) $get('first_order_enabled')),
                        Textarea::make('first_order_notes')
                            ->label(__('project.quick_order.fields.notes'))
                            ->rows(3)
                            ->columnSpanFull()
                            ->visible(fn (Get $get): bool => (bool) $get('first_order_enabled')),
                    ])
                    ->visibleOn('create')
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
            ->get()
            ->mapWithKeys(fn (User $user): array => [
                $user->id => "{$user->name} ({$user->email})",
            ])
            ->all();
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
