<?php

namespace Database\Seeders;

use App\Enums\ClientStatus;
use App\Enums\ClientType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PricingType;
use App\Enums\ProjectStatus;
use App\Enums\ProjectTimelineStageStatus;
use App\Enums\UnitType;
use App\Helpers\Price;
use App\Models\Client;
use App\Models\Company;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectTimelineStage;
use App\Models\Service;
use App\Models\Subservice;
use App\Models\User;
use App\Support\TenantRoleProvisioner;
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

        $renovationService = Service::updateOrCreate(
            ['company_id' => $buildBoard->id, 'name' => 'Apartment renovation'],
            [
                'description' => 'Full apartment repair and finishing package.',
                'base_price_amount' => Price::toAmount(12000),
                'is_active' => true,
            ],
        );

        Subservice::updateOrCreate(
            ['service_id' => $renovationService->id, 'name' => 'Bathroom renovation'],
            [
                'description' => 'Demolition, plumbing, tiling and finishing.',
                'price_amount' => Price::toAmount(6200),
                'pricing_type' => PricingType::Fixed,
                'unit' => UnitType::Room,
                'estimated_duration' => '2-3 weeks',
                'is_active' => true,
            ],
        );

        Subservice::updateOrCreate(
            ['service_id' => $renovationService->id, 'name' => 'Wall painting'],
            [
                'description' => 'Surface preparation and painting.',
                'price_amount' => Price::toAmount(18),
                'pricing_type' => PricingType::PerSquareMeter,
                'unit' => UnitType::SquareMeter,
                'estimated_duration' => '1-3 days',
                'is_active' => true,
            ],
        );

        $kitchenService = Service::updateOrCreate(
            ['company_id' => $renova->id, 'name' => 'Kitchen renovation'],
            [
                'description' => 'Kitchen planning, demolition, installation and finishing.',
                'base_price_amount' => Price::toAmount(9000),
                'is_active' => true,
            ],
        );

        Subservice::updateOrCreate(
            ['service_id' => $kitchenService->id, 'name' => 'Cabinet installation'],
            [
                'description' => 'Kitchen cabinet assembly and installation.',
                'price_amount' => Price::toAmount(2500),
                'pricing_type' => PricingType::Fixed,
                'unit' => UnitType::Service,
                'estimated_duration' => '3-5 days',
                'is_active' => true,
            ],
        );

        $buildApartmentOrder = Order::updateOrCreate(
            ['company_id' => $buildBoard->id, 'number' => 'ORD-0001'],
            [
                'client_id' => $ninoClient->id,
                'title' => 'Apartment renovation estimate',
                'status' => OrderStatus::Pending,
                'deadline' => now()->addWeeks(3)->toDateString(),
                'progress' => 25,
                'estimated_price_amount' => Price::toAmount(18500),
                'notes' => 'Initial order before choosing detailed subservices.',
            ],
        );

        $bathroomOrder = Order::updateOrCreate(
            ['company_id' => $buildBoard->id, 'number' => 'ORD-0002'],
            [
                'client_id' => $giorgiClient->id,
                'title' => 'Bathroom repair request',
                'status' => OrderStatus::Pending,
                'deadline' => now()->addWeeks(2)->toDateString(),
                'progress' => 15,
                'estimated_price_amount' => Price::toAmount(6200),
                'notes' => 'Waiting for client confirmation.',
            ],
        );

        $buildApartmentProject = Project::updateOrCreate(
            ['company_id' => $buildBoard->id, 'title' => 'Apartment renovation project'],
            [
                'client_id' => $ninoClient->id,
                'status' => ProjectStatus::Active,
                'deadline' => now()->addWeeks(8)->toDateString(),
                'progress' => 42,
                'budget_amount' => Price::toAmount(24700),
                'notes' => 'Main BuildBoard demo project with multiple orders.',
            ],
        );

        $buildApartmentOrder->forceFill([
            'project_id' => $buildApartmentProject->id,
        ])->save();

        $bathroomOrder->forceFill([
            'project_id' => $buildApartmentProject->id,
        ])->save();

        $renovaKitchenOrder = Order::updateOrCreate(
            ['company_id' => $renova->id, 'number' => 'ORD-0001'],
            [
                'client_id' => $mariamClient->id,
                'title' => 'Kitchen renovation order',
                'status' => OrderStatus::Approved,
                'deadline' => now()->addWeeks(4)->toDateString(),
                'progress' => 35,
                'estimated_price_amount' => Price::toAmount(9400),
                'notes' => 'Approved sample order for Renova Demo.',
            ],
        );

        $renovaKitchenProject = Project::updateOrCreate(
            ['company_id' => $renova->id, 'title' => 'Kitchen renovation project'],
            [
                'client_id' => $mariamClient->id,
                'status' => ProjectStatus::Active,
                'deadline' => now()->addWeeks(6)->toDateString(),
                'progress' => 35,
                'budget_amount' => $renovaKitchenOrder->estimated_price_amount,
                'notes' => 'Sample project created from an approved order.',
            ],
        );

        $renovaKitchenOrder->forceFill([
            'project_id' => $renovaKitchenProject->id,
        ])->save();

        Payment::updateOrCreate(
            ['company_id' => $buildBoard->id, 'order_id' => $buildApartmentOrder->id, 'reference' => 'BB-DEPOSIT-001'],
            [
                'project_id' => $buildApartmentOrder->project?->id,
                'client_id' => $ninoClient->id,
                'amount' => Price::toAmount(5500),
                'status' => PaymentStatus::Paid,
                'paid_at' => now()->subDays(2)->toDateString(),
                'method' => PaymentMethod::BankTransfer->value,
                'notes' => 'Initial deposit for the renovation estimate.',
            ],
        );

        Payment::updateOrCreate(
            ['company_id' => $renova->id, 'order_id' => $renovaKitchenOrder->id, 'reference' => 'RN-DEPOSIT-001'],
            [
                'project_id' => $renovaKitchenProject->id,
                'client_id' => $mariamClient->id,
                'amount' => Price::toAmount(4000),
                'status' => PaymentStatus::Paid,
                'paid_at' => now()->subWeek()->toDateString(),
                'method' => PaymentMethod::Card->value,
                'notes' => 'Deposit paid before demolition stage.',
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

        $superAdmin = User::updateOrCreate(
            ['email' => 'super@test.com'],
            ['name' => 'Super Admin', 'password' => Hash::make('password')]
        );

        $buildApartmentOrder->forceFill([
            'assigned_user_id' => $manager->id,
        ])->save();

        $bathroomOrder->forceFill([
            'assigned_user_id' => $worker->id,
        ])->save();

        $renovaKitchenOrder->forceFill([
            'assigned_user_id' => $manager->id,
        ])->save();

        $buildBoard->members()->syncWithoutDetaching([
            $admin->id,
            $manager->id,
            $worker->id,
            $superAdmin->id,
        ]);

        $renova->members()->syncWithoutDetaching([
            $manager->id,
            $superAdmin->id,
        ]);

        try {
            $provisioner = app(TenantRoleProvisioner::class);

            Company::query()->each(function (Company $company) use ($provisioner, $superAdmin): void {
                $provisioner->provision($company);
                $provisioner->assignSuperAdmin($company, $superAdmin);
            });

            $provisioner->assignCompanyAdmin($buildBoard, $admin);
            $provisioner->assignRole($buildBoard, $manager, 'manager');
            $provisioner->assignRole($buildBoard, $worker, 'worker');
            $provisioner->assignRole($renova, $manager, 'manager');
        } finally {
            setPermissionsTeamId($originalTeamId);
        }
    }
}
