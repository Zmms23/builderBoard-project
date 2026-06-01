<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 15;

    protected static ?string $slug = 'members';

    protected static bool $isScopedToTenant = false;

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('user.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('user.columns.email'))
                    ->searchable()
                    ->copyable(),
                TextColumn::make('current_role')
                    ->label(__('user.columns.role'))
                    ->state(fn (User $record): string => $record->getRoleNames()->first() ?? 'none')
                    ->formatStateUsing(fn (string $state): string => __('user.roles.' . $state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'company_admin' => 'primary',
                        'manager' => 'warning',
                        'worker' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label(__('user.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label(__('user.filters.role'))
                    ->options(function (): array {
                        $tenant = Filament::getTenant();

                        if (! $tenant) {
                            return [];
                        }

                        return Role::query()
                            ->where('company_id', $tenant->getKey())
                            ->orderBy('name')
                            ->pluck('name', 'name')
                            ->mapWithKeys(fn (string $roleName, string $key): array => [$key => __('user.roles.' . $roleName)])
                            ->all();
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        $role = $data['value'] ?? null;

                        if (blank($role)) {
                            return $query;
                        }

                        return $query->whereHas('roles', function (Builder $roleQuery) use ($role): void {
                            $roleQuery->where('name', $role);
                        });
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $tenant = Filament::getTenant();

        return parent::getEloquentQuery()
            ->when(
                filled($tenant),
                fn (Builder $query): Builder => $query->whereHas('companies', fn (Builder $companyQuery): Builder => $companyQuery->whereKey($tenant->getKey())),
                fn (Builder $query): Builder => $query->whereRaw('1 = 0'),
            )
            ->with('roles');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getModelLabel(): string
    {
        return __('user.navigation.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('user.navigation.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('user.navigation.plural');
    }

    public static function getNavigationBadge(): ?string
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return null;
        }

        return (string) static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('user.navigation.badge');
    }
}
