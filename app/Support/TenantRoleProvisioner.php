<?php

namespace App\Support;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Database\QueryException;
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

            $superAdminRole = $this->role($company, 'super_admin');
            $companyAdminRole = $this->role($company, 'company_admin');
            $managerRole = $this->role($company, 'manager');
            $workerRole = $this->role($company, 'worker');

            $superAdminRole->syncPermissions($this->permissions(RolePermissions::superAdmin()));
            $companyAdminRole->syncPermissions($this->permissions(RolePermissions::companyAdmin()));
            $managerRole->syncPermissions($this->permissions(RolePermissions::manager()));
            $workerRole->syncPermissions($this->permissions(RolePermissions::worker()));

            $this->forgetCachedPermissions();
        } finally {
            setPermissionsTeamId($originalTeamId);
        }
    }

    public function assignSuperAdmin(Company $company, User $user): void
    {
        $this->assignRole($company, $user, 'super_admin');
    }

    public function assignCompanyAdmin(Company $company, User $user): void
    {
        $this->assignRole($company, $user, 'company_admin');
    }

    public function assignRole(Company $company, User $user, string $roleName): void
    {
        $company->members()->syncWithoutDetaching([$user->id]);

        $originalTeamId = getPermissionsTeamId();

        setPermissionsTeamId($company->id);

        try {
            $role = $this->role($company, $roleName);

            $user->unsetRelation('roles');
            $user->syncRoles([$role]);

            $this->forgetCachedPermissions();
        } finally {
            setPermissionsTeamId($originalTeamId);
        }
    }

    public function removeFromCompany(Company $company, User $user): void
    {
        $originalTeamId = getPermissionsTeamId();

        setPermissionsTeamId($company->id);

        try {
            $user->unsetRelation('roles');
            $user->syncRoles([]);

            $company->members()->detach($user->id);

            $this->forgetCachedPermissions();
        } finally {
            setPermissionsTeamId($originalTeamId);
        }
    }

    private function role(Company $company, string $name): Role
    {
        $attributes = [
            'name' => $name,
            'guard_name' => 'web',
            'company_id' => $company->id,
        ];

        $role = Role::query()
            ->where($attributes)
            ->first();

        if ($role instanceof Role) {
            return $role;
        }

        try {
            $roleId = DB::table(config('permission.table_names.roles'))
                ->insertGetId([
                    ...$attributes,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            return Role::query()->findOrFail($roleId);
        } catch (QueryException $exception) {
            $existingRole = Role::query()
                ->where($attributes)
                ->first();

            if ($existingRole instanceof Role) {
                return $existingRole;
            }

            throw $exception;
        }
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
