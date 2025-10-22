<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = ['id'];

    //convert to array from json field
    protected $casts = [
        'permissions' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    /**
     * the permission is existing in the role
     * @param string $permissionName
     */
    public function hasPermissionTo(string $permissionName): bool
    {
        // the permission is existing in the permissions
        return in_array($permissionName, $this->permissions ?? []);
    }
}
