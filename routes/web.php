<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

if (app()->isLocal()) {
    Route::post('/admin/login', function () {
        $credentials = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, request()->boolean('remember'))) {
            return redirect()
                ->to(url('/admin/login'))
                ->with('plain_login_error', __('auth.failed'))
                ->withInput(['email' => $credentials['email']]);
        }

        request()->session()->regenerate();

        /** @var User|null $authenticatedUser */
        $authenticatedUser = Auth::user();

        $fallbackTenantId = $authenticatedUser?->companies()->orderBy('name')->value('companies.id');

        request()->session()->save();

        return redirect()->intended($fallbackTenantId ? url("/admin/{$fallbackTenantId}") : url('/admin'), 303);
    });

    Route::domain('{tenant}.zura-meskhi-project.test')
        ->group(function (): void {
            Route::get('/admin/{path?}', function (string $tenant, ?string $path = null) {
                $tenantId = ctype_digit($tenant)
                    ? (int) $tenant
                    : Company::query()
                        ->get(['id', 'name'])
                        ->first(fn (Company $company): bool => str($company->name)->slug()->value() === $tenant)
                        ?->getKey();

                if ($tenantId === null) {
                    return redirect()->to('http://zura-meskhi-project.test/admin');
                }

                if (blank($path)) {
                    return redirect()->to("http://zura-meskhi-project.test/admin/{$tenantId}");
                }

                $firstSegment = str($path)->before('/')->value();

                if (in_array($firstSegment, ['login', 'register', 'password-reset', 'email-verification', 'profile'], true)) {
                    return redirect()->to("http://zura-meskhi-project.test/admin/{$path}");
                }

                if ($firstSegment === (string) $tenantId) {
                    return redirect()->to("http://zura-meskhi-project.test/admin/{$path}");
                }

                return redirect()->to("http://zura-meskhi-project.test/admin/{$tenantId}/{$path}");
            })->where('path', '.*');
        });
}

Route::get('/', function () {
    return view('welcome');
});
