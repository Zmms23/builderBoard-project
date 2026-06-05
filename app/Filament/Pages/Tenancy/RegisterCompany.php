<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Support\TenantRoleProvisioner;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class RegisterCompany extends RegisterTenant
{
    public static function getLabel(): string
    {
        return __('tenancy.actions.register_company');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('tenancy.fields.company_name'))
                    ->required()
                    ->maxLength(255),
            ]);
    }

    /**
     * @param  array{name: string}  $data
     */
    protected function handleRegistration(array $data): Company
    {
        $company = Company::create($data);

        /** @var User $user */
        $user = Auth::user();
        $company->members()->syncWithoutDetaching([$user->id]);

        $originalTeamId = getPermissionsTeamId();

        app(TenantRoleProvisioner::class)->provision($company);

        setPermissionsTeamId($company->id);

        try {
            $companyAdminRole = Role::firstOrCreate([
                'name' => 'company_admin',
                'guard_name' => 'web',
                'company_id' => $company->id,
            ]);

            $user->unsetRelation('roles');
            $user->assignRole($companyAdminRole);
        } finally {
            setPermissionsTeamId($originalTeamId);
        }

        return $company;
    }
}
