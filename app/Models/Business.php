<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Business extends Model
{
    protected $fillable = [
        'is_prime',
        'business_type',
        'parent_business_id',
        'owner_user_id',
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
        'can_manage_roles' => 'boolean',
        'status' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(UserProfile::class);
    }

    // সাব-বিজনেস রিলেশন
    public function parentBusiness(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'parent_business_id');
    }

    public function subBusinesses(): HasMany
    {
        return $this->hasMany(Business::class, 'parent_business_id');
    }
}
