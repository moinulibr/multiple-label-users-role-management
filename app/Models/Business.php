<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Business extends Model
{
    protected $guarded = ['id'];
    protected $fillable = [
        'is_prime',
        'business_type',
        'hierarchy_level_id',
        'parent_business_id',
        'user_id',
        'default_login',
        'name',
        'slug',
        'email',
        'phone',
        'phone2',
        'address',
        'website',
        'can_manage_roles',
        'status'
    ];

    protected $casts = [
        'is_prime' => 'boolean',
        'default_login' => 'boolean',
        'can_manage_roles' => 'boolean',
        'status' => 'boolean',
    ];

    //start----new----
    public function parent()
    {
        return $this->belongsTo(Business::class, 'parent_business_id');
    }

    public function children()
    {
        return $this->hasMany(Business::class, 'parent_business_id');
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }
    //end----new----


    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(UserProfile::class);
    }

    public function parentBusiness(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'parent_business_id');
    }

    public function subBusinesses(): HasMany
    {
        return $this->hasMany(Business::class, 'parent_business_id');
    }
}
