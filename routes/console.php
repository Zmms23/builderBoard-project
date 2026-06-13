<?php

use App\Models\Company;
use App\Support\TenantRoleProvisioner;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('tenants:sync-roles', function (TenantRoleProvisioner $provisioner) {
    $originalTeamId = getPermissionsTeamId();
    $count = 0;

    try {
        Company::query()->orderBy('id')->each(function (Company $company) use ($provisioner, &$count): void {
            $provisioner->provision($company);
            $count++;
        });
    } finally {
        setPermissionsTeamId($originalTeamId);
    }

    $this->info("Synced tenant roles for {$count} companies.");
})->purpose('Sync tenant role permissions for all companies');
