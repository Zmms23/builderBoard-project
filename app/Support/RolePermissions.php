<?php

namespace App\Support;

class RolePermissions
{
    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            'Create:Client',
            'Create:Order',
            'Create:Project',
            'Create:Role',
            'Create:Service',
            'Create:User',
            'Delete:Client',
            'Delete:Order',
            'Delete:Project',
            'Delete:Role',
            'Delete:Service',
            'Delete:User',
            'DeleteAny:Client',
            'DeleteAny:Order',
            'DeleteAny:Project',
            'DeleteAny:Role',
            'DeleteAny:Service',
            'DeleteAny:User',
            'ForceDelete:Client',
            'ForceDelete:Order',
            'ForceDelete:Project',
            'ForceDelete:Role',
            'ForceDelete:Service',
            'ForceDelete:User',
            'ForceDeleteAny:Client',
            'ForceDeleteAny:Order',
            'ForceDeleteAny:Project',
            'ForceDeleteAny:Role',
            'ForceDeleteAny:Service',
            'ForceDeleteAny:User',
            'Reorder:Client',
            'Reorder:Order',
            'Reorder:Project',
            'Reorder:Role',
            'Reorder:Service',
            'Reorder:User',
            'Replicate:Client',
            'Replicate:Order',
            'Replicate:Project',
            'Replicate:Role',
            'Replicate:Service',
            'Replicate:User',
            'Restore:Client',
            'Restore:Order',
            'Restore:Project',
            'Restore:Role',
            'Restore:Service',
            'Restore:User',
            'RestoreAny:Client',
            'RestoreAny:Order',
            'RestoreAny:Project',
            'RestoreAny:Role',
            'RestoreAny:Service',
            'RestoreAny:User',
            'Update:Client',
            'Update:Order',
            'Update:Project',
            'Update:Role',
            'Update:Service',
            'Update:User',
            'View:Client',
            'View:CompanySettings',
            'View:Order',
            'View:Project',
            'View:Role',
            'View:Service',
            'View:User',
            'ViewAny:Client',
            'ViewAny:Order',
            'ViewAny:Project',
            'ViewAny:Role',
            'ViewAny:Service',
            'ViewAny:User',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function companyAdmin(): array
    {
        return self::all();
    }

    /**
     * @return array<int, string>
     */
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

    /**
     * @return array<int, string>
     */
    public static function worker(): array
    {
        return [
            'ViewAny:Service',
            'View:Service',
        ];
    }
}
