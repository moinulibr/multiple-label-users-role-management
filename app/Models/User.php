<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasRolesAndPermissions;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'secondary_phone',
        'provider',
        'provider_id',
        'status',
        'is_developer',
        'phone_verified_at',
        'email_verified_at',
        'profile_picture'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'is_developer' => 'boolean',
        'password' => 'hashed',
    ];

    public function profiles(): HasMany
    {
        return $this->hasMany(UserProfile::class);
    }

    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }
    
    public function otpAttempts(): HasMany
    {
        return $this->hasMany(OtpAttempt::class);
    }

    /**
     * Accessor define for profile_picture attribute.
     * 
     * when access profile_picture from database, then 
     * it will be converted to public URL
     * * Laravel 9+:
     */
    protected function profilePicture(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                //1. if There is no path stored in the database or the file comes from Google/Facebook
                if (!$value || Str::startsWith($value, ['http', 'https'])) {
                    // If it's a public URL, then just return it url
                    return $value;
                }

                //2. Convert the path to public URL
                $url = Storage::disk('public')->url($value);

                // It's removing to duble slass
                $cleanedUrl = Str::replaceFirst('//storage', '/storage', $url);

                return $cleanedUrl;
            },
            // No need to Mutator
        );
    }
}
