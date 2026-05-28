<?php

namespace App\Http\Controllers;

use App\Settings\CompanySettings;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyLogoController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'logo' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
        ]);

        $settings = app(CompanySettings::class);

        if (filled($settings->logo_path) && Storage::disk('public')->exists($settings->logo_path)) {
            Storage::disk('public')->delete($settings->logo_path);
        }

        $settings->logo_path = $data['logo']->store('company-logos', 'public');
        $settings->save();

        Notification::make()
            ->success()
            ->title(__('settings.notifications.logo_uploaded'))
            ->send();

        return back();
    }

    public function destroy(): RedirectResponse
    {
        $settings = app(CompanySettings::class);

        if (filled($settings->logo_path) && Storage::disk('public')->exists($settings->logo_path)) {
            Storage::disk('public')->delete($settings->logo_path);
        }

        $settings->logo_path = null;
        $settings->save();

        Notification::make()
            ->success()
            ->title(__('settings.notifications.logo_removed'))
            ->send();

        return back();
    }
}
