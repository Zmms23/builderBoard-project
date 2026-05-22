<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    public function __invoke(string $locale): RedirectResponse
    {
        abort_unless(array_key_exists($locale, config('locales.available')), 404);

        session(['locale' => $locale]);
        app()->setLocale($locale);

        return back();
    }
}
