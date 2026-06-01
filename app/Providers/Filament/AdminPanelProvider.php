<?php

namespace App\Providers\Filament;

use App\Enums\Locale;
use App\Filament\Pages\Tenancy\EditCompanyProfile;
use App\Filament\Pages\Tenancy\RegisterCompany;
use App\Http\Middleware\SetLocale;
use App\Models\Company;
use App\Settings\CompanySettings as CompanySettingsData;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\SetPermissionsTeam;




class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->profile()
            ->login()

            ->brandName(fn (): string => filament()->getTenant()?->name ?? config('app.name'))

            ->brandLogo(fn (): ?string => $this->settings()?->logo_path? asset('storage/' . $this->settings()->logo_path): asset('images/download.png'))
               

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
                Action::make('locale')
                    ->label(fn (): string => Locale::current()->getLabel())
                    ->icon(fn (): Heroicon => Locale::current()->getIcon())
                    ->color(fn (): string => Locale::current()->getColor())
                    ->schema([
                        Select::make('locale')
                            ->label(fn (): string => __('locale.language'))
                            ->options(fn (): array => Locale::options())
                            ->required()
                            ->native(false),
                    ])
                    ->fillForm(fn (): array => [
                        'locale' => app()->getLocale(),
                    ])
                    ->action(function (array $data) {
                        $locale = Locale::tryFrom($data['locale']);

                        abort_unless(
                            $locale !== null && in_array($locale->value, Locale::values(), true),
                            404
                        );

                        session(['locale' => $locale->value]);
                        app()->setLocale($locale->value);

                        return redirect(request()->header('Referer'));
                    })
                    ->sort(91),
            ])

            ->tenant(Company::class)
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
