<?php

namespace App\Filament\Resources;

use App\Enums\ClientStatus;
use App\Enums\ClientType;
use App\Filament\Resources\ClientResource\Pages\CreateClient;
use App\Filament\Resources\ClientResource\Pages\EditClient;
use App\Filament\Resources\ClientResource\Pages\ListClients;
use App\Models\Client;
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

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $tenantOwnershipRelationshipName = 'company';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('client.sections.details'))
                    ->schema([
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
                            ->default(ClientStatus::Lead)
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
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('client.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('client.columns.type'))
                    ->formatStateUsing(fn (ClientType | string | null $state): string => static::formatClientType($state))
                    ->badge()
                    ->color(fn (ClientType | string | null $state): string => static::clientTypeColor($state)),
                TextColumn::make('status')
                    ->label(__('client.columns.status'))
                    ->formatStateUsing(fn (ClientStatus | string | null $state): string => static::formatClientStatus($state))
                    ->badge()
                    ->color(fn (ClientStatus | string | null $state): string => static::clientStatusColor($state)),
                TextColumn::make('phone')
                    ->label(__('client.columns.phone'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('client.columns.email'))
                    ->searchable()
                    ->copyable(),
                TextColumn::make('address')
                    ->label(__('client.columns.address'))
                    ->limit(40)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('notes')
                    ->label(__('client.columns.notes'))
                    ->limit(40)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('client.columns.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('client.filters.type'))
                    ->options(ClientType::class),
                SelectFilter::make('status')
                    ->label(__('client.filters.status'))
                    ->options(ClientStatus::class),
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
            'index' => ListClients::route('/'),
            'create' => CreateClient::route('/create'),
            'edit' => EditClient::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('client.navigation.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('client.navigation.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('client.navigation.plural');
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
        return __('client.navigation.badge');
    }

    private static function formatClientType(ClientType | string | null $state): string
    {
        return $state instanceof ClientType
            ? $state->getLabel()
            : ClientType::tryFrom((string) $state)?->getLabel() ?? '-';
    }

    private static function clientTypeColor(ClientType | string | null $state): string
    {
        $type = $state instanceof ClientType
            ? $state
            : ClientType::tryFrom((string) $state);

        return match ($type) {
            ClientType::Company => 'primary',
            ClientType::Person => 'gray',
            default => 'gray',
        };
    }

    private static function formatClientStatus(ClientStatus | string | null $state): string
    {
        return $state instanceof ClientStatus
            ? $state->getLabel()
            : ClientStatus::tryFrom((string) $state)?->getLabel() ?? '-';
    }

    private static function clientStatusColor(ClientStatus | string | null $state): string
    {
        $status = $state instanceof ClientStatus
            ? $state
            : ClientStatus::tryFrom((string) $state);

        return match ($status) {
            ClientStatus::Lead => 'warning',
            ClientStatus::Active => 'success',
            ClientStatus::Inactive => 'gray',
            default => 'gray',
        };
    }
}
