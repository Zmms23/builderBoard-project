<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Company;
use App\Models\User;
use App\Support\TenantRoleProvisioner;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected string $roleName = 'worker';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['company_name'] = (string) (Filament::getTenant()?->getAttribute('name') ?? '');
        $data['role_name'] = $this->currentRoleName();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->roleName = (string) ($data['role_name'] ?? 'worker');

        unset($data['company_name'], $data['role_name'], $data['password']);

        return $data;
    }

    protected function afterSave(): void
    {
        $tenant = Filament::getTenant();
        $record = $this->getRecord();

        if (! $tenant instanceof Company || ! $record instanceof User) {
            return;
        }

        app(TenantRoleProvisioner::class)->assignRole($tenant, $record, $this->roleName);
    }

    private function currentRoleName(): string
    {
        $record = $this->getRecord();

        if (! $record instanceof User) {
            return 'worker';
        }

        $record->unsetRelation('roles');

        $roleName = $record->getRoleNames()->first();

        return is_string($roleName) && array_key_exists($roleName, UserResource::assignableRoleOptions())
            ? $roleName
            : 'worker';
    }
}
