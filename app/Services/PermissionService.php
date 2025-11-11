<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\UserContextManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PermissionService
{
    /**
     * Register all gates dynamically from config/app_permissions.php
     */
    public static function registerPermissions(): void
    {
        $modules = config('app_permissions.modules', []);

        foreach ($modules as $module => $data) {
            foreach ($data['actions'] as $action => $rules) {

                // Global permission (no context)
                if (!empty($rules['isAllowedToAllContextLayer'])) {
                    $key = "{$module}.{$action}";
                    //Gate::define($key, fn($user) => $user->hasPermission($key));
                    Gate::define($key, fn($user) => app(\App\Services\UserContextManager::class)
                        ->getCurrentProfile()?->hasPermission($key));
                }

                // Context-based permissions
                foreach ($rules['contextLayer'] ?? [] as $context) {
                    $key = "{$module}.{$action}";
                    //Gate::define($key, fn($user) => $user->hasPermission($key));
                    Gate::define($key, fn($user) => app(\App\Services\UserContextManager::class)
                        ->getCurrentProfile()?->hasPermission($key));
                }
            }
        }
    }

    /**
     * Smart permission check (with or without context)
     */
    public static function check(string $permission): bool
    {
        $contextManager = app(UserContextManager::class);
        $user = Auth::user();
        if (!$user) return false;

        $profile = $contextManager->getCurrentProfile();
        // no active profile
        if (!$profile) {
            return false;
        }

        // Developer / Super Admin always pass
        if ($user->is_developer || $contextManager->isSuperAdmin()) {
            return true;
        }

        [$module, $action] = explode('.', $permission);
        $rules = config("app_permissions.modules.{$module}.actions.{$action}", []);

        // Global allow
        if (!empty($rules['isAllowedToAllContextLayer']) && $rules['isAllowedToAllContextLayer'] === true) {
            return $profile->hasPermission("{$module}.{$action}");
        }

        // Context-based
        $userContext = $contextManager->getUserContextLayer(); // e.g. 'secondary'
        $allowedContexts = $rules['contextLayer'] ?? [];

        if (in_array($userContext, $allowedContexts)) {
            // if context allowed, main permission ("users.create") will be check
            return $profile->hasPermission("{$module}.{$action}");
        }

        // Not allowed for this context
        return false;
    }
}