<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    protected $guarded = ['id'];

    /* protected $casts = [
        'permissions' => 'array',
    ]; */

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user')->withPivot('business_id')->withTimestamps();
    }


    public function hasPermissionTo(string $permissionName): bool
    {
        // check cached collection if loaded
        if ($this->relationLoaded('permissions')) {
            return $this->permissions->contains(fn($p) => $p->name === $permissionName);
        }

        return $this->permissions()->where('name', $permissionName)->exists();
    }

    // Role.php
    public function userProfiles()
    {
        return $this->belongsToMany(UserProfile::class, 'role_user_profiles')
            ->withPivot('business_id')
            ->withTimestamps();
    }
}
