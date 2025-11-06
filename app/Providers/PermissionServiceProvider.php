<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Services\PermissionService;
use App\Http\Middleware\AuthorizePermission;

class PermissionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // register all gates
        PermissionService::registerPermissions();

        // register middleware alias for Laravel 12+
        Route::aliasMiddleware('permission', AuthorizePermission::class);
    }
}
