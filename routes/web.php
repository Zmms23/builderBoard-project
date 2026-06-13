<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

$centralUrl = rtrim((string) config('app.url'), '/');
$centralHost = parse_url($centralUrl, PHP_URL_HOST) ?: (string) config('app.central_domain');

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

if (app()->isLocal() && filled($centralHost)) {
    Route::domain("{tenant}.{$centralHost}")
        ->group(function () use ($centralUrl): void {
            Route::get('/admin/{path?}', function (string $tenant, ?string $path = null) use ($centralUrl) {
                $tenantId = ctype_digit($tenant)
                    ? (int) $tenant
                    : Company::query()
                        ->get(['id', 'name'])
                        ->first(fn (Company $company): bool => str($company->name)->slug()->value() === $tenant)
                        ?->getKey();

                if ($tenantId === null) {
                    return redirect()->to("{$centralUrl}/admin");
                }

                if (blank($path)) {
                    return redirect()->to("{$centralUrl}/admin/{$tenantId}");
                }

                $firstSegment = str($path)->before('/')->value();

                if (in_array($firstSegment, ['login', 'register', 'password-reset', 'email-verification', 'profile'], true)) {
                    return redirect()->to("{$centralUrl}/admin/{$path}");
                }

                if ($firstSegment === (string) $tenantId) {
                    return redirect()->to("{$centralUrl}/admin/{$path}");
                }

                return redirect()->to("{$centralUrl}/admin/{$tenantId}/{$path}");
            })->where('path', '.*');
        });
}

Route::get('/', function () {
    /** @var User|null $authenticatedUser */
    $authenticatedUser = Auth::user();

    $fallbackTenantId = $authenticatedUser?->companies()->orderBy('name')->value('companies.id');

    return view('welcome', [
        'adminUrl' => $fallbackTenantId ? url("/admin/{$fallbackTenantId}") : url('/admin/login'),
        'isAuthenticated' => Auth::check(),
    ]);
});

Route::redirect('/login', '/admin/login');

Route::get('/locale/{locale}', function (string $locale) {
    $availableLocales = config('app.available_locales', []);

    if (in_array($locale, $availableLocales, true)) {
        request()->session()->put('locale', $locale);
    }

    return redirect()->back();
});
