<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Company;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;

class RegisterCompany extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register company';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Company name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(array $data): Company
    {
        $company = Company::create($data);

        $company->members()->attach(auth()->id());

        return $company;
    }
}
