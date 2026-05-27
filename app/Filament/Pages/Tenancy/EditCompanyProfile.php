<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

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
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                        $set('slug', Str::slug($state ?? '').'-'.Str::lower(Str::random(6)));
                    }),
                Hidden::make('slug')
                    ->required(),
            ]);
    }
}
