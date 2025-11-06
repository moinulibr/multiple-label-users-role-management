<?php

namespace App\Providers;

use App\Http\Middleware\AuthorizePermission;
use App\View\Composers\SidebarComposer;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(Router $router): void
    {
        // View composer for sidebar
        View::composer('layouts.sidebar', SidebarComposer::class);

        // Register alias for middleware
        $router->aliasMiddleware('permission', AuthorizePermission::class);
    }
}
