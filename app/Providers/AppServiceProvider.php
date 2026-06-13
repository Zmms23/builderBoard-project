<?php

namespace App\Providers;

use App\Filament\Auth\Responses\LoginResponse as CustomLoginResponse;
use App\Models\Permission;
use App\Models\ProofUpload;
use App\Models\Role;
use App\Observers\ProofUploadObserver;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\PermissionRegistrar;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LoginResponseContract::class, CustomLoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $compiledViewsPath = storage_path('framework/views-compiled');

        if (! is_dir($compiledViewsPath)) {
            mkdir($compiledViewsPath, 0777, true);
        }

        app(PermissionRegistrar::class)
            ->setPermissionClass(Permission::class)
            ->setRoleClass(Role::class);

        ProofUpload::observe(ProofUploadObserver::class);
    }
}
