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

    private ?int $adminUserId = null;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->adminUserId = (int) ($data['admin_user_id'] ?? 0);

        unset($data['admin_user_id']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $admin = User::query()->find($this->adminUserId);

        if (! $this->record instanceof Company || ! $admin instanceof User) {
            return;
        }

        $provisioner = app(TenantRoleProvisioner::class);

        $provisioner->provision($this->record);
        $provisioner->assignCompanyAdmin($this->record, $admin);

        $creator = Filament::auth()->user();

        if ($creator instanceof User) {
            $provisioner->assignSuperAdmin($this->record, $creator);
        }
    }
}
