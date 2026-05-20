<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_staff_roles_can_access_admin_panel(): void
    {
        $this->assertTrue(UserRole::SuperAdmin->canAccessAdminPanel());
        $this->assertTrue(UserRole::CompanyAdmin->canAccessAdminPanel());
        $this->assertTrue(UserRole::Manager->canAccessAdminPanel());
        $this->assertTrue(UserRole::Worker->canAccessAdminPanel());
    }

    public function test_client_role_can_not_access_admin_panel(): void
    {
        $this->assertFalse(UserRole::Client->canAccessAdminPanel());
    }
}
