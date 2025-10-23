<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserType extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'dashboard_key',
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
