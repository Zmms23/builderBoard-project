<?php

namespace App\Providers\Filament;

use App\Enums\Locale;
use App\Filament\Pages\Tenancy\EditCompanyProfile;
use App\Filament\Pages\Tenancy\RegisterCompany;
use App\Http\Middleware\SetLocale;
use App\Models\Company;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandLogo(fn (): ?string => $this->getTenantLogoUrl())
            ->brandLogoHeight('2rem')
            ->colors(fn (): array => $this->getTenantColors())
            ->userMenuItems([
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
                    ->action(function (array $data){
                        $locale = Locale::tryFrom($data['locale']);

                        abort_unless($locale !== null && in_array($locale->value, Locale::values(), true), 404);

                        session(['locale' => $locale->value]);
                        app()->setLocale($locale->value);

                        return redirect(request()->header('Referer'));
                    })
                    ->sort(91),
            ])
            ->tenant(Company::class)
            ->tenantRegistration(RegisterCompany::class)
            ->tenantProfile(EditCompanyProfile::class)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
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

    /**
     * @return array<string, array<int, string>>
     */
    private function getTenantColors(): array
    {
        $primaryColor = $this->getTenant()?->setting?->primary_color;

        return [
            'primary' => filled($primaryColor) ? Color::hex($primaryColor) : Color::Amber,
        ];
    }

    private function getTenantLogoUrl(): ?string
    {
        $logoPath = $this->getTenant()?->setting?->logo_path;

        return filled($logoPath) ? Storage::disk('public')->url($logoPath) : null;
    }

    private function getTenant(): ?Company
    {
        $tenant = Filament::getTenant();

        return $tenant instanceof Company ? $tenant : null;
    }
}
