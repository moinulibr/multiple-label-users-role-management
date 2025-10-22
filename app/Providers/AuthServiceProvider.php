<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];
    
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerPolicies();

        // It's make a gate for all permission from config
        $permissionsArray = config('app_permissions.modules', []);

        foreach ($permissionsArray as $module => $actions) {
            foreach ($actions as $action) {
                $permissionName = "{$module}.{$action}";

                // Gate::define() [string $name, callable $callback] [every permission checking - sending to the User model]
                Gate::define($permissionName, function (User $user) use ($permissionName) {
                    return $user->hasPermission($permissionName);
                });
            }
        }
    }
}