<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoTenantSeeder extends Seeder
{
    public function run(): void
    {
        $originalTeamId = getPermissionsTeamId();

        $buildBoard = Company::firstOrCreate(
            ['slug' => 'buildboard-demo'],
            ['name' => 'BuildBoard Demo']
        );

        $renova = Company::firstOrCreate(
            ['slug' => 'renova-demo'],
            ['name' => 'Renova Demo']
        );

        $admin = User::firstOrCreate(
            ['email' => 'zura@test.com'],
            ['name' => 'Zura', 'password' => Hash::make('password')]
        );

        $manager = User::firstOrCreate(
            ['email' => 'manager@test.com'],
            ['name' => 'Manager', 'password' => Hash::make('password')]
        );

        $worker = User::firstOrCreate(
            ['email' => 'worker@test.com'],
            ['name' => 'Worker', 'password' => Hash::make('password')]
        );

        $buildBoard->members()->syncWithoutDetaching([
            $admin->id,
            $manager->id,
            $worker->id,
        ]);

        $renova->members()->syncWithoutDetaching([
            $manager->id,
        ]);

        try {
            setPermissionsTeamId($buildBoard->id);

            $companyAdminRole = Role::firstOrCreate([
                'name' => 'company_admin',
                'guard_name' => 'web',
                'company_id' => $buildBoard->id,
            ]);

            $managerRole = Role::firstOrCreate([
                'name' => 'manager',
                'guard_name' => 'web',
                'company_id' => $buildBoard->id,
            ]);

            $workerRole = Role::firstOrCreate([
                'name' => 'worker',
                'guard_name' => 'web',
                'company_id' => $buildBoard->id,
            ]);

            $admin->unsetRelation('roles');
            $admin->syncRoles([$companyAdminRole]);

            $manager->unsetRelation('roles');
            $manager->syncRoles([$managerRole]);

            $worker->unsetRelation('roles');
            $worker->syncRoles([$workerRole]);

            setPermissionsTeamId($renova->id);

            $renovaManagerRole = Role::firstOrCreate([
                'name' => 'manager',
                'guard_name' => 'web',
                'company_id' => $renova->id,
            ]);

            $manager->unsetRelation('roles');
            $manager->syncRoles([$renovaManagerRole]);
        } finally {
            setPermissionsTeamId($originalTeamId);
        }
    }
}
