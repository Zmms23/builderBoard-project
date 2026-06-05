<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Resources\CompanyResource;
use App\Models\Company;
use App\Models\User;
use App\Support\TenantRoleProvisioner;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;

    protected ?int $adminUserId = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->adminUserId = (int) ($data['admin_user_id'] ?? 0);

        unset($data['admin_user_id']);

        return $data;
    }

    protected function afterCreate(): void
    {
        if (! $this->record instanceof Company) {
            return;
        }

        $provisioner = app(TenantRoleProvisioner::class);

        if ($this->adminUserId > 0) {
            $admin = User::query()->find($this->adminUserId);

            if ($admin instanceof User) {
                $provisioner->assignCompanyAdmin($this->record, $admin);
            }
        }

        $currentUser = Filament::auth()->user();

        if ($currentUser instanceof User) {
            $provisioner->assignSuperAdmin($this->record, $currentUser);
        }
    }
}
