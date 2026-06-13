<?php

namespace App\Filament\Auth\Responses;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $panel = Filament::getCurrentOrDefaultPanel();
        $user = Filament::auth()->user();

        $request->session()->save();

        if ($user && method_exists($user, 'getTenants') && $panel->hasTenancy()) {
            $tenant = collect($user->getTenants($panel))
                ->sortBy(fn (Model $tenant): string => (string) $tenant->getAttribute('name'))
                ->first();

            if ($tenant instanceof Model) {
                return redirect()->intended(Filament::getUrl($tenant));
            }
        }

        return redirect()->intended(Filament::getUrl());
    }
}
