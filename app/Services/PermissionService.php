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
     * ✅ Register all gates dynamically from config/app_permissions.php
     */
    public static function registerPermissions(): void
    {
        $modules = config('app_permissions.modules', []);

        foreach ($modules as $module => $data) {
            foreach ($data['actions'] as $action => $rules) {

                // 🔹 Global permission (no context)
                if (!empty($rules['all_contexts'])) {
                    $key = "{$module}.{$action}";
                    Gate::define($key, fn($user) => $user->hasPermission($key));
                }

                // 🔹 Context-based permissions
                foreach ($rules['contexts'] ?? [] as $context) {
                    $key = "{$module}.{$action}";
                    Gate::define($key, fn($user) => $user->hasPermission($key));
                }
            }
        }
    }

    /**
     * ✅ Smart permission check (with or without context)
     */
    public static function check(string $permission): bool
    {
        $contextManager = app(UserContextManager::class);
        $user = Auth::user();
        if (!$user) return false;

        // Developer / Super Admin always pass
        if ($user->is_developer || $contextManager->isSuperAdmin()) {
            return true;
        }

        [$module, $action] = explode('.', $permission);
        $rules = config("app_permissions.modules.{$module}.actions.{$action}", []);

        // 1️⃣ Global allow
        if (!empty($rules['all_contexts']) && $rules['all_contexts'] === true) {
            return $user->hasPermission("{$module}.{$action}");
        }

        // 2️⃣ Context-based
        $userContext = $contextManager->getUserContextLayer(); // e.g. 'secondary'
        $allowedContexts = $rules['contexts'] ?? [];

        if (in_array($userContext, $allowedContexts)) {
            // context allowed হলে শুধু main permission ("users.create") check করো
            return $user->hasPermission("{$module}.{$action}");
        }

        // 3️⃣ Not allowed for this context
        return false;
    }
}


/*
    public static function check(string $permission): bool
    {
        $contextManager = app(UserContextManager::class);
        $isSuperAdminFlag = $contextManager->isSuperAdmin();
       
        $user = Auth::user();
        if (!$user) return false;

        // 🔹 Developer or Super Admin bypass
        if ($user->is_developer || $isSuperAdminFlag) {
            return true;
        }

        $parts = explode('.', $permission);

        // module.action (no context)
        if (count($parts) === 2) {
            [$module, $action] = $parts;
            $rules = config("app_permissions.modules.{$module}.actions.{$action}", []);

            // global
            if (!empty($rules['all_contexts'])) {
                return $user->hasPermission("{$module}.{$action}");
            }

            // context based
            foreach ($rules['contexts'] ?? [] as $context) {
                if ($user->hasPermission("{$module}.{$action}.{$context}")) {
                    return true;
                }
            }

            return false;
        }
        
        // 🔸 module.action.context
        return $user->hasPermission($permission);
    }
*/
