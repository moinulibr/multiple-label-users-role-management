<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasCustomPermissions;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasCustomPermissions;

    protected $with = ['roles'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'provider_id',
        'provider',
        'password',
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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
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
