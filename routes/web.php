<?php

use App\Http\Controllers\CompanyLogoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/admin/company-settings/logo', [CompanyLogoController::class, 'store'])
        ->name('admin.company-settings.logo.store');

    Route::delete('/admin/company-settings/logo', [CompanyLogoController::class, 'destroy'])
        ->name('admin.company-settings.logo.destroy');
});
