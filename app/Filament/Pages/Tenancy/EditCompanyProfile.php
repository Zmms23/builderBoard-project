<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Schema;

class EditCompanyProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return __('tenancy.pages.company_profile');
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
}
