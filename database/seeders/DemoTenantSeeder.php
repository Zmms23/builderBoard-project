<?php

namespace Database\Seeders;

use App\Models\Client;
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
            ['name' => 'BuildBoard Demo']
        );

        $renova = Company::firstOrCreate(
            ['name' => 'Renova Demo']
        );

        Client::updateOrCreate(
            ['company_id' => $buildBoard->id, 'email' => 'nino.client@example.com'],
            [
                'name' => 'Nino Client',
                'phone' => '+995599111222',
                'address' => 'Tbilisi, Saburtalo',
            ],
        );

        Client::updateOrCreate(
            ['company_id' => $buildBoard->id, 'email' => 'giorgi.client@example.com'],
            [
                'name' => 'Giorgi Client',
                'phone' => '+995577333444',
                'address' => 'Tbilisi, Vake',
            ],
        );

        Client::updateOrCreate(
            ['company_id' => $renova->id, 'email' => 'mariam.client@example.com'],
            [
                'name' => 'Mariam Client',
                'phone' => '+995555444333',
                'address' => 'Batumi, Rustaveli Avenue',
            ],
        );

        Client::updateOrCreate(
            ['company_id' => $renova->id, 'email' => 'levan.client@example.com'],
            [
                'name' => 'Levan Client',
                'phone' => '+995591222333',
                'address' => 'Batumi, Gorgiladze Street',
            ],
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@test.com'],
            ['name' => 'Admin', 'password' => Hash::make('password')]
        );

        $manager = User::updateOrCreate(
            ['email' => 'manager@test.com'],
            ['name' => 'Manager', 'password' => Hash::make('password')]
        );

        $worker = User::updateOrCreate(
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
