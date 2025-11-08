<?php

namespace App\Traits;

use App\Services\UserContextManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

trait HasRolesAndPermissions
{
    /**
     * HasRolesAndPermissions
     * - Caches permissions per user per context (business_id or user_profile_id fallback)
     * - Provides clearPermissionCache() for invalidation
     */
    public function roles()
    {
        return $this->belongsToMany(\App\Models\Role::class, 'role_user')
            ->withPivot('business_id')
            ->withTimestamps();
    }


    /**
     * Get all unique permissions for this user in given business context
     *
     * @param int|null $businessId
     * @return array
     */
    public function getAllPermissions($businessId = null): array
    {
        // context identifier: prefer businessId, fallback to user_profile_id, else 'global'
        $contextId = $businessId ?? $this->user_profile_id ?? 'global';
        $cacheKey = "user_permissions:{$this->id}:{$contextId}";
        //$cacheKey = "user_permissions:{$this->id}:{$businessId}";

        return Cache::remember($cacheKey, now()->addMinutes(1), function () use ($businessId) {
            $permissions = [];

            // eager load roles if not loaded
            $roles = $this->roles()
                ->select('roles.id', 'roles.permissions', 'role_user.business_id')
                ->get();

            foreach ($this->roles as $role) {
                // If role is business-scoped and businessId provided, ensure match
                if ($businessId !== null && $role->business_id !== null && $role->business_id != $businessId) {
                    continue;
                }

                // always take permissions from JSON column
                $rolePermissions = json_decode($role->permissions, true) ?? [];
                if (is_array($rolePermissions)) {
                    $permissions = array_merge($permissions, $rolePermissions);
                }
            }
            //Log::info("permissions trait - ".json_encode($permissions));
            //$contextManager = app(UserContextManager::class);
            //$userContext = $contextManager->getUserContextLayer();
            //Log::info("userContext layer - ".json_encode($userContext));
            return array_unique($permissions);
        });
    }

    public function hasPermission(string $permission, $businessId = null): bool
    {
        // developer & super_admin bypass handled elsewhere, but safe to check if needed
        $contextManager = app(UserContextManager::class);
        $isSuperAdminFlag = $contextManager->isSuperAdmin();

        if ($this->is_developer) return true;
        if ($isSuperAdminFlag) return true;
        
        $allPermissions = $this->getAllPermissions($businessId);
        return in_array($permission, $allPermissions);
    }

    /**
     * Clear this user's permission cache for a given business/context
     */
    public function clearPermissionCache($businessId = null): void
    {
        $contextId = $businessId ?? $this->user_profile_id ?? 'global';
        $cacheKey = "user_permissions:{$this->id}:{$contextId}";
        Cache::forget($cacheKey);
    }
}


/*
$permissionsConfig = config('app_permissions.user_contexts_layer', []);
Log::info("permissions config - " . json_encode($permissionsConfig));
$contextManager = app(UserContextManager::class);
$getUserContextLayer = $contextManager->getUserContextLayer();
$getUserContextLayerId = $contextManager->getUserContextLayerId();
Log::info("permissions config layer- " . $getUserContextLayer);

$contextValue = config("app_permissions.user_contexts_layer.{$getUserContextLayerId}");
Log::info("permissions config value- " . $contextValue);
*/