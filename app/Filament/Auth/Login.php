<?php

namespace App\Filament\Auth;

use Illuminate\Support\Facades\App;

class Login extends \Filament\Auth\Pages\Login
{
    public function getView(): string
    {
        if (App::isLocal()) {
            return 'filament.auth.local-login-page';
        }

        return parent::getView();
    }
}
