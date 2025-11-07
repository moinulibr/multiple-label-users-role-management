<?php

namespace App\Traits;

use App\Services\UserContextManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

trait HasRolesAndPermissions
{

    public function roles()
    {
        return $this->belongsToMany(\App\Models\Role::class, 'role_user')
            ->withPivot('business_id')
            ->withTimestamps();
    }


    public function getAllPermissions($businessId = null): array
    {
        $cacheKey = "user_permissions:{$this->id}:{$businessId}";

        return Cache::remember($cacheKey, now()->addMinutes(1), function () use ($businessId) {
            $permissions = [];

            foreach ($this->roles as $role) {
                if ($businessId !== null && $role->business_id !== null && $role->business_id != $businessId) {
                    continue;
                }

                // always take permissions from JSON column
                $rolePermissions = json_decode($role->permissions, true) ?? [];
                $permissions = array_merge($permissions, $rolePermissions);
            }
            Log::info("permissions trait - ".json_encode($permissions));
            $contextManager = app(UserContextManager::class);
            $userContext = $contextManager->getUserContextLayer();
            Log::info("userContext layer - ".json_encode($userContext));
            return array_unique($permissions);
        });
    }

    public function hasPermission(string $permission, $businessId = null): bool
    {
        $contextManager = app(UserContextManager::class);
        $isSuperAdminFlag = $contextManager->isSuperAdmin();

        if ($this->is_developer) return true;
        if ($isSuperAdminFlag) return true;
        
        $allPermissions = $this->getAllPermissions($businessId);
        return in_array($permission, $allPermissions);
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