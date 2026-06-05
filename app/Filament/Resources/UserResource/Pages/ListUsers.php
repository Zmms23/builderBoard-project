<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Company;
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
                ->label(__('user.actions.add_existing.label'))
                ->visible(fn (): bool => Filament::auth()->user()?->can('Create:User') === true)
                ->form([
                    Select::make('user_id')
                        ->label(__('user.actions.add_existing.user'))
                        ->searchable()
                        ->options(fn (): array => $this->availableUserOptions())
                        ->getSearchResultsUsing(fn (string $search): array => $this->availableUserOptions($search))
                        ->getOptionLabelUsing(fn ($value): ?string => $this->userOptionLabel(User::query()->find($value)))
                        ->required(),

                    Select::make('role_name')
                        ->label(__('user.actions.add_existing.role'))
                        ->options(UserResource::assignableRoleOptions())
                        ->default('worker')
                        ->native(false)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    abort_unless(Filament::auth()->user()?->can('Create:User') === true, 403);

                    $tenant = Filament::getTenant();

                    if (! $tenant instanceof Company) {
                        return;
                    }

                    $user = User::query()->findOrFail($data['user_id']);

                    app(TenantRoleProvisioner::class)->assignRole($tenant, $user, $data['role_name']);
                }),

            CreateAction::make(),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function availableUserOptions(?string $search = null): array
    {
        $tenant = Filament::getTenant();

        if (! $tenant instanceof Company) {
            return [];
        }

        return User::query()
            ->whereDoesntHave(
                'companies',
                fn (Builder $query): Builder => $query->whereKey($tenant->getKey()),
            )
            ->when(filled($search), function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->limit(50)
            ->get()
            ->mapWithKeys(fn (User $user): array => [$user->id => $this->userOptionLabel($user)])
            ->all();
    }

    private function userOptionLabel(?User $user): ?string
    {
        return $user
            ? "{$user->name} ({$user->email})"
            : null;
    }
}
