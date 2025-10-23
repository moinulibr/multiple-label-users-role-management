<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'otp_code',
        'expires_at',
        'purpose',
        'recipient',
        'used_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];
    

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
