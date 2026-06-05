<?php

namespace App\Providers\Filament;

use App\Filament\Auth\Login;
use App\Filament\Pages\Tenancy\EditCompanyProfile;
use App\Filament\Pages\Tenancy\RegisterCompany;
use App\Http\Middleware\SetPermissionsTeam;
use App\Http\Middleware\SetLocale;
use App\Models\Company;
use App\Settings\CompanySettings as CompanySettingsData;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use CraftForge\FilamentLanguageSwitcher\FilamentLanguageSwitcherPlugin;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Str;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $tenantDomain = app()->isLocal()
            ? null
            : '{tenant}.zura-meskhi-project.test';

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->profile()
            ->login(Login::class)

            ->brandName(fn (): string => filament()->getTenant()?->name ?? config('app.name'))

            ->brandLogo(function (): string {
                $logoPath = $this->settings()?->logo_path;

                return $logoPath
                    ? asset("storage/{$logoPath}")
                    : asset('images/download.png');
            })
            ->brandLogoHeight('2rem')
            ->colors(function (): array {
                try {
                    return [
                        'primary' => app(CompanySettingsData::class)->primary_color ?? '#f59e0b',
                    ];
                } catch (\Throwable $e) {
                    report($e);

                    return [
                        'primary' => '#f59e0b',
                    ];
                }
            })
            ->userMenuItems([
                Action::make('currentRole')
                    ->label(fn (): string => __('user.menu.current_role', ['role' => $this->currentRoleLabel()]))
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->disabled()
                    ->visible(fn (): bool => filament()->auth()->check())
                    ->sort(90),
            ])

            ->domain('zura-meskhi-project.test')
            ->tenant(Company::class)
            ->tenantDomain($tenantDomain)
            ->tenantRegistration(RegisterCompany::class)
            ->tenantProfile(EditCompanyProfile::class)

            ->tenantMiddleware([
                SetPermissionsTeam::class,
            ], isPersistent: true)

            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )

            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages'
            )

            ->pages([
                Dashboard::class,
            ])

            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets'
            )

            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])

            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                SetLocale::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])

            ->plugins([
                FilamentLanguageSwitcherPlugin::make()
                    ->locales(fn (): array => config('app.available_locales', ['en', 'ka']))
                    ->rememberLocale()
                    ->showOnAuthPages(),
                FilamentShieldPlugin::make(),
            ])

            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    private function settings(): ?CompanySettingsData
    {
        return app(CompanySettingsData::class);
    }

    private function currentRoleLabel(): string
    {
        $user = filament()->auth()->user();

        if (! $user) {
            return __('user.roles.none');
        }

        if (! method_exists($user, 'getRoleNames')) {
            return __('user.roles.none');
        }

        $role = $user->getRoleNames()->first();

        if (blank($role)) {
            return __('user.roles.none');
        }

        $translatedRole = __('user.roles.' . $role);

        if ($translatedRole === 'user.roles.' . $role) {
            return Str::of($role)->replace('_', ' ')->headline()->toString();
        }

        return $translatedRole;
    }
}
