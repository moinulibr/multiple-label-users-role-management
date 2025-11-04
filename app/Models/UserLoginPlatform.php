<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserLoginPlatform extends Model
{
    // user_login_platforms table
    protected $fillable = [
        'name',
        'platform_key',
        'platform_hash_key',
        'login_template_hash_key',
        'status',
    ];

    protected $casts = [
        // Crucial for JSON field validation
        'login_template_hash_key' => 'array',
        'status' => 'boolean',
    ];
}
