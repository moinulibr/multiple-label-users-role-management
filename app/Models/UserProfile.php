<?php

namespace App\Models;

use App\Traits\HasRolesAndPermissions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasRolesAndPermissions;
    
    protected $fillable = [
        'user_id',
        'user_type_id',
        'business_id',
        'default_login',
        'status',
        'profile_picture'
    ];

    protected $casts = [
        'default_login' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userType(): BelongsTo
    {
        return $this->belongsTo(UserType::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user_profiles')
            ->withPivot('business_id')
            ->withTimestamps();
    }
}
