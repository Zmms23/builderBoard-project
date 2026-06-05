<?php

namespace App\Support;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Collection;
use Spatie\Permission\PermissionRegistrar;

class TenantRoleProvisioner
{
    public function provision(Company $company): void
    {
        $this->forgetCachedPermissions();

        $originalTeamId = getPermissionsTeamId();

        setPermissionsTeamId($company->id);

        try {
            $this->ensurePermissions();

            $companyAdminRole = $this->role($company, 'company_admin');
            $managerRole = $this->role($company, 'manager');
            $workerRole = $this->role($company, 'worker');

            $companyAdminRole->syncPermissions($this->permissions(RolePermissions::companyAdmin()));
            $managerRole->syncPermissions($this->permissions(RolePermissions::manager()));
            $workerRole->syncPermissions($this->permissions(RolePermissions::worker()));

            $this->forgetCachedPermissions();
        } finally {
            setPermissionsTeamId($originalTeamId);
        }
    }

    private function role(Company $company, string $name): Role
    {
        return Role::firstOrCreate([
            'name' => $name,
            'guard_name' => 'web',
            'company_id' => $company->id,
        ]);
    }

    /**
     * @param  array<int, string>  $names
     * @return Collection<int, Permission>
     */
    private function permissions(array $names): Collection
    {
        if ($names === ['*']) {
            return Permission::query()->get();
        }

        return Permission::query()
            ->whereIn('name', $names)
            ->get();
    }

    private function ensurePermissions(): void
    {
        foreach (RolePermissions::all() as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }

    private function forgetCachedPermissions(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
