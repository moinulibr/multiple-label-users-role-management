<?php

namespace App\Providers;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\View\Composers\SidebarComposer;
use App\Http\Middleware\AuthorizePermission;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // view composer registered here
        // the SidebarComposer class will be run before rendering the 'layouts.sidebar' view
        View::composer('layouts.sidebar', SidebarComposer::class);

        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('permission', AuthorizePermission::class);
    }
}
