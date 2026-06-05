<?php

namespace App\Support;

class RolePermissions
{
    public static function companyAdmin(): array
    {
        return ['*'];
    }

    public static function manager(): array
    {
        return [
            'ViewAny:Client',
            'View:Client',
            'Create:Client',
            'Update:Client',

            'ViewAny:Project',
            'View:Project',
            'Create:Project',
            'Update:Project',

            'ViewAny:Order',
            'View:Order',
            'Create:Order',
            'Update:Order',

            'ViewAny:Service',
            'View:Service',
        ];
    }

    public static function worker(): array
    {
        return [
            'ViewAny:Client',
            'View:Client',

            'ViewAny:Project',
            'View:Project',

            'ViewAny:Order',
            'View:Order',

            'ViewAny:Service',
            'View:Service',
        ];
    }
}
