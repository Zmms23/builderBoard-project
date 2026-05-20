<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case CompanyAdmin = 'company_admin';
    case Manager = 'manager';
    case Worker = 'worker';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::CompanyAdmin => 'Company Admin',
            self::Manager => 'Manager',
            self::Worker => 'Worker',
            self::Client => 'Client',
        };
    }

    public function canAccessAdminPanel(): bool
    {
        return in_array($this, [
            self::SuperAdmin,
            self::CompanyAdmin,
            self::Manager,
            self::Worker,
        ], true);
    }
}
