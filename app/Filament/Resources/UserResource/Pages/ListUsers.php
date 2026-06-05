<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Support\TenantRoleProvisioner;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addExistingUser')
                ->label('Add existing user')
                ->form([
                    Select::make('user_id')
                        ->label('User')
                        ->options(fn (): array => User::query()
                            ->whereDoesntHave('companies', fn (Builder $query): Builder =>
                                $query->whereKey(Filament::getTenant()?->getKey())
                            )
                            ->pluck('name', 'id')
                            ->toArray())
                        ->searchable()
                        ->required(),

                    Select::make('role_name')
                        ->label('Role')
                        ->options(UserResource::assignableRoleOptions())
                        ->default('worker')
                        ->native(false)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $tenant = Filament::getTenant();
                    $user = User::findOrFail($data['user_id']);

                    $tenant->members()->syncWithoutDetaching([
                        $user->id,
                    ]);

                    app(TenantRoleProvisioner::class)->assignRole(
                        $tenant,
                        $user,
                        $data['role_name']
                    );
                }),

            CreateAction::make(),
        ];
    }
}