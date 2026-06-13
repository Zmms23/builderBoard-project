<?php

namespace App\Support;

class RolePermissions
{
    /**
     * @var array<int, string>
     */
    private const RESOURCE_ACTIONS = [
        'Create',
        'Delete',
        'DeleteAny',
        'ForceDelete',
        'ForceDeleteAny',
        'Reorder',
        'Replicate',
        'Restore',
        'RestoreAny',
        'Update',
        'View',
        'ViewAny',
    ];

    /**
     * @var array<int, string>
     */
    private const TENANT_RESOURCES = [
        'Client',
        'Order',
        'Payment',
        'Project',
        'ProofUpload',
        'Role',
        'Service',
        'User',
    ];

    /**
     * @var array<int, string>
     */
    private const CENTRAL_RESOURCES = [
        'Company',
    ];

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            ...self::resourcePermissions([
                ...self::CENTRAL_RESOURCES,
                ...self::TENANT_RESOURCES,
            ]),
            'View:CompanySettings',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function superAdmin(): array
    {
        return self::all();
    }

    /**
     * @return array<int, string>
     */
    public static function companyAdmin(): array
    {
        return [
            ...self::resourcePermissions(self::TENANT_RESOURCES),
            'View:CompanySettings',
        ];
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

            'ViewAny:Payment',
            'View:Payment',
            'Create:Payment',
            'Update:Payment',

            'ViewAny:ProofUpload',
            'View:ProofUpload',
            'Create:ProofUpload',
            'Update:ProofUpload',
            'Delete:ProofUpload',

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
            'ViewAny:Order',
            'View:Order',

            'ViewAny:Project',
            'View:Project',

            'ViewAny:ProofUpload',
            'View:ProofUpload',
            'Create:ProofUpload',
            'Update:ProofUpload',

            'ViewAny:Service',
            'View:Service',
        ];
    }

    /**
     * @param  array<int, string>  $resources
     * @return array<int, string>
     */
    private static function resourcePermissions(array $resources): array
    {
        $permissions = [];

        foreach ($resources as $resource) {
            foreach (self::RESOURCE_ACTIONS as $action) {
                $permissions[] = "{$action}:{$resource}";
            }
        }

        return $permissions;
    }
}
