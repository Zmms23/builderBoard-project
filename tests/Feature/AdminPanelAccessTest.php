<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Support\TenantRoleProvisioner;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_admin_can_access_their_company_tenant(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create();

        app(TenantRoleProvisioner::class)->provision($company);
        app(TenantRoleProvisioner::class)->assignCompanyAdmin($company, $user);

        $this->assertTrue($user->canAccessPanel(Filament::getPanel('admin')));
        $this->assertTrue($user->canAccessTenant($company));
    }

    public function test_user_without_company_membership_can_not_access_that_tenant(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create();

        $this->assertTrue($user->canAccessPanel(Filament::getPanel('admin')));
        $this->assertFalse($user->canAccessTenant($company));
    }
}
