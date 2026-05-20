<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_company_admin_role_can_access_admin_panel(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'company_admin']);

        $user->assignRole($role);

        $this->assertTrue($user->canAccessPanel(Filament::getPanel('admin')));
    }

    public function test_user_without_staff_role_can_not_access_admin_panel(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->canAccessPanel(Filament::getPanel('admin')));
    }
}
