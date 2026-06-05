<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Company;
use App\Models\User;
use App\Support\TenantRoleProvisioner;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected string $roleName = 'worker';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->roleName = (string) ($data['role_name'] ?? 'worker');

        unset($data['role_name']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $tenant = Filament::getTenant();

        if (! $tenant instanceof Company || ! $this->record instanceof User) {
            return;
        }

        app(TenantRoleProvisioner::class)->assignRole($tenant, $this->record, $this->roleName);
    }
}
