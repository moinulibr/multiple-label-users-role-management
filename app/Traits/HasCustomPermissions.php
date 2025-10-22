<?php

namespace App\Traits;

use App\Models\Role;

trait HasCustomPermissions
{
    /**
     * The relations between User and Role models.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Check the certain permission exists on the user.
     * @param string $permissionName
     */
    public function hasPermission(string $permissionName): bool
    {
        // it's for super admin
        if ($this->roles->contains('name', 'super_admin')) {
             return true;
        }

        // other roles
        foreach ($this->roles as $role) {
            if ($role->hasPermissionTo($permissionName)) {
                return true;
            }
        }

        return false;
    }
}