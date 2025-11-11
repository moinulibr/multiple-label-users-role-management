<?php

namespace App\Traits;

use App\Models\Role;
use App\Services\UserContextManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait HasRolesAndPermissions
{
    public function roles()
    {
        // user -> roles through user_profile -> role_user_profiles
        return $this->belongsToMany(
            Role::class,
            'role_user_profiles',     // pivot table
            'user_profile_id',        // foreign key on pivot table pointing to UserProfile
            'role_id'                 // foreign key on pivot table pointing to Role
        )
        ->withPivot(['business_id', 'user_profile_id'])
        ->withTimestamps();
    }

    /**
     * Get all permissions for this user based on active profile & business context.
     */
    public function getAllPermissions($business_id = null): array
    {
        $contextManager = app(UserContextManager::class);
        $cacheKey = $contextManager->getPermissionCacheKey();
        
        return Cache::remember($cacheKey, now()->addMinutes(2), function () use ($contextManager) {
            $permissions = [];

            $userProfile = $contextManager->getCurrentProfile();
            $businessId = $business_id ?? $contextManager->getBusinessId();
            if (!$userProfile) {
                return [];
            }

            $rolesQuery = $this->roles()
                ->select('roles.id', 'roles.permissions', 'role_user_profiles.business_id', 'role_user_profiles.user_profile_id');
            if($businessId){
                $rolesQuery->wherePivot('user_profile_id', $userProfile->id);
            }
            $roles = $rolesQuery->wherePivot('business_id', $businessId)->get();

            foreach ($roles as $role) {
                if ($businessId !== null && $role->business_id !== null && $role->business_id != $businessId) {
                    continue;
                }

                $rolePermissions = json_decode($role->permissions, true) ?? [];
                if (is_array($rolePermissions)) {
                    $permissions = array_merge($permissions, $rolePermissions);
                }
            }

            return array_unique($permissions);
        });
    }

    /**
     * Check if the user has a specific permission.
     */
    public function hasPermission(string $permission, $businessId = null): bool
    {
        $contextManager = app(UserContextManager::class);
        $business_id = $businessId ?? $contextManager->getBusinessId();

        if ($this->is_developer || $contextManager->isSuperAdmin()) {
            return true;
        }

        $allPermissions = $this->getAllPermissions($business_id);
        return in_array($permission, $allPermissions);
    }

}
