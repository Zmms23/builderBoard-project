<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages\CreateCompany;
use App\Filament\Resources\CompanyResource\Pages\EditCompany;
use App\Filament\Resources\CompanyResource\Pages\ListCompanies;
use App\Models\Company;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isScopedToTenant = false;

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('company.sections.details'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('company.fields.name'))
                            ->required()
                            ->maxLength(255),
                        Select::make('admin_user_id')
                            ->label(__('company.fields.primary_admin'))
                            ->helperText(__('company.help.primary_admin'))
                            ->options(fn (): array => static::userOptions())
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required()
                            ->visibleOn('create')
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label(__('company.fields.admin_name'))
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->label(__('company.fields.admin_email'))
                                    ->email()
                                    ->unique(User::class, 'email')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('password')
                                    ->label(__('company.fields.admin_password'))
                                    ->password()
                                    ->required()
                                    ->minLength(8)
                                    ->maxLength(255),
                            ])
                            ->createOptionUsing(fn (array $data): int => (int) User::query()->create($data)->getKey()),
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
                    ->label(__('company.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('members_count')
                    ->label(__('company.columns.members'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('company.columns.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompanies::route('/'),
            'create' => CreateCompany::route('/create'),
            'edit' => EditCompany::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('members');
    }

    public static function getModelLabel(): string
    {
        return __('company.navigation.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('company.navigation.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('company.navigation.plural');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::query()->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('company.navigation.badge');
    }

    /**
     * @return array<int, string>
     */
    private static function userOptions(): array
    {
        return User::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (User $user): array => [
                $user->id => "{$user->name} ({$user->email})",
            ])
            ->all();
    }
}
