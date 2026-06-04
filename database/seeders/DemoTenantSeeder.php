<?php

namespace Database\Seeders;

use App\Enums\ClientStatus;
use App\Enums\ClientType;
use App\Enums\OrderStatus;
use App\Enums\ProjectStatus;
use App\Enums\ProjectTaskStatus;
use App\Enums\ProjectTimelineStageStatus;
use App\Helpers\Price;
use App\Models\Client;
use App\Models\Company;
use App\Models\Order;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\ProjectTimelineStage;
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

        $ninoClient = Client::updateOrCreate(
            ['company_id' => $buildBoard->id, 'email' => 'nino.client@example.com'],
            [
                'name' => 'Nino Client',
                'type' => ClientType::Person,
                'status' => ClientStatus::Active,
                'phone' => '+995599111222',
                'address' => 'Tbilisi, Saburtalo',
                'notes' => 'Interested in apartment renovation progress tracking.',
            ],
        );

        $giorgiClient = Client::updateOrCreate(
            ['company_id' => $buildBoard->id, 'email' => 'giorgi.client@example.com'],
            [
                'name' => 'Giorgi Client',
                'type' => ClientType::Person,
                'status' => ClientStatus::Lead,
                'phone' => '+995577333444',
                'address' => 'Tbilisi, Vake',
                'notes' => 'Needs an estimate before approving the order.',
            ],
        );

        $mariamClient = Client::updateOrCreate(
            ['company_id' => $renova->id, 'email' => 'mariam.client@example.com'],
            [
                'name' => 'Mariam Client',
                'type' => ClientType::Person,
                'status' => ClientStatus::Active,
                'phone' => '+995555444333',
                'address' => 'Batumi, Rustaveli Avenue',
                'notes' => 'Prefers updates by email.',
            ],
        );

        Client::updateOrCreate(
            ['company_id' => $renova->id, 'email' => 'levan.client@example.com'],
            [
                'name' => 'Levan Construction LLC',
                'type' => ClientType::Company,
                'status' => ClientStatus::Inactive,
                'phone' => '+995591222333',
                'address' => 'Batumi, Gorgiladze Street',
                'notes' => 'Corporate client, currently inactive.',
            ],
        );

        Order::updateOrCreate(
            ['company_id' => $buildBoard->id, 'number' => 'ORD-0001'],
            [
                'client_id' => $ninoClient->id,
                'title' => 'Apartment renovation estimate',
                'status' => OrderStatus::Pending,
                'estimated_price_amount' => Price::toAmount(18500),
                'notes' => 'Initial order before choosing detailed subservices.',
            ],
        );

        Order::updateOrCreate(
            ['company_id' => $buildBoard->id, 'number' => 'ORD-0002'],
            [
                'client_id' => $giorgiClient->id,
                'title' => 'Bathroom repair request',
                'status' => OrderStatus::Draft,
                'estimated_price_amount' => Price::toAmount(6200),
                'notes' => 'Waiting for client confirmation.',
            ],
        );

        $renovaKitchenOrder = Order::updateOrCreate(
            ['company_id' => $renova->id, 'number' => 'ORD-0001'],
            [
                'client_id' => $mariamClient->id,
                'title' => 'Kitchen renovation order',
                'status' => OrderStatus::Approved,
                'estimated_price_amount' => Price::toAmount(9400),
                'notes' => 'Approved sample order for Renova Demo.',
            ],
        );

        $renovaKitchenProject = Project::updateOrCreate(
            ['order_id' => $renovaKitchenOrder->id],
            [
                'company_id' => $renova->id,
                'client_id' => $mariamClient->id,
                'title' => 'Kitchen renovation project',
                'status' => ProjectStatus::Active,
                'deadline' => now()->addWeeks(6)->toDateString(),
                'progress' => 35,
                'budget_amount' => $renovaKitchenOrder->estimated_price_amount,
                'notes' => 'Sample project created from an approved order.',
            ],
        );

        ProjectTimelineStage::updateOrCreate(
            ['project_id' => $renovaKitchenProject->id, 'name' => 'Planning'],
            [
                'status' => ProjectTimelineStageStatus::Completed,
                'sort' => 1,
                'starts_at' => now()->subWeeks(2)->toDateString(),
                'ends_at' => now()->subWeek()->toDateString(),
                'notes' => 'Initial measurements and schedule confirmed.',
            ],
        );

        ProjectTimelineStage::updateOrCreate(
            ['project_id' => $renovaKitchenProject->id, 'name' => 'Demolition'],
            [
                'status' => ProjectTimelineStageStatus::InProgress,
                'sort' => 2,
                'starts_at' => now()->subDays(3)->toDateString(),
                'ends_at' => now()->addDays(2)->toDateString(),
                'notes' => 'Old cabinets and tiles are being removed.',
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

        ProjectTask::updateOrCreate(
            ['project_id' => $renovaKitchenProject->id, 'name' => 'Confirm electrical points'],
            [
                'assigned_to_id' => $manager->id,
                'status' => ProjectTaskStatus::InProgress,
                'sort' => 1,
                'deadline' => now()->addDays(4)->toDateString(),
                'budget_amount' => Price::toAmount(450),
                'notes' => 'Confirm socket locations before wall finishing starts.',
            ],
        );

        ProjectTask::updateOrCreate(
            ['project_id' => $renovaKitchenProject->id, 'name' => 'Order kitchen tiles'],
            [
                'assigned_to_id' => $manager->id,
                'status' => ProjectTaskStatus::Todo,
                'sort' => 2,
                'deadline' => now()->addWeek()->toDateString(),
                'budget_amount' => Price::toAmount(1200),
                'notes' => 'Client approved the first tile option.',
            ],
        );

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
