<?php

namespace App\Filament\Auth;

use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class Login extends \Filament\Auth\Pages\Login
{
    protected Width|string|null $maxContentWidth = Width::ExtraLarge;

    public function hasLogo(): bool
    {
        return false;
    }

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public function getView(): string
    {
        return 'filament.auth.local-login-page';
    }
}
