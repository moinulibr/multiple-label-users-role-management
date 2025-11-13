<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserType extends Model
{
    public const TYPE_SUPER_ADMIN = 'super_admin';
    public const TYPE_ADMIN = 'admin';
    public const TYPE_BUSINESS_OWNER = 'business_owner';
    public const TYPE_EMPLOYEE = 'employee';

    protected $fillable = [
        'name',
        'display_name',
        'dashboard_key',
        'visiblity',
        'login_template_key',
        'login_template_hash_key',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function profiles(): HasMany
    {
        return $this->hasMany(UserProfile::class);
    }
}
