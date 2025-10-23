<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'user_type_id',
        'business_id',
        'is_primary',
        'status',
        'profile_picture'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
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

    /**
     * Mock function to fetch profiles based on the new complex logic.
     * This mimics fetching from the database where user.status=1 and user_profile.status=1
     *
     * @param int $userId
     * @return \Illuminate\Support\Collection
     */
    public static function getActiveProfilesForUser($userId)
    {
        // Mock data to test all scenarios requested by the user:
        // Case 1: Single Profile (User ID 10) - Auto login
        // Case 2: Multiple Profiles, business_id = null (User ID 20) - General selection
        // Case 3: Multiple Profiles, same business_id (User ID 30) - Role selection for one business
        // Case 4: Multiple Profiles, different business_id (User ID 40) - Business then Role selection

        $profiles = collect([]);

        if ($userId == 10) {
            // Case 1: Single profile, direct verification
            $profiles->push((object)['business_id' => 101, 'business_name' => 'Single Business', 'role' => 'Manager', 'status' => 1]);
        }

        if ($userId == 20) {
            // Case 2: Multiple general profiles (business_id = null)
            $profiles->push((object)['business_id' => null, 'business_name' => 'Personal Account A', 'role' => 'General User', 'status' => 1]);
            $profiles->push((object)['business_id' => null, 'business_name' => 'Personal Account B', 'role' => 'Reviewer', 'status' => 1]);
        }

        if ($userId == 30) {
            // Case 3: Multiple roles for the SAME business
            $profiles->push((object)['business_id' => 301, 'business_name' => 'Same Corp Ltd', 'role' => 'Administrator', 'status' => 1]);
            $profiles->push((object)['business_id' => 301, 'business_name' => 'Same Corp Ltd', 'role' => 'Employee', 'status' => 1]);
        }

        if ($userId == 40) {
            // Case 4: Multiple roles for DIFFERENT businesses
            $profiles->push((object)['business_id' => 401, 'business_name' => 'Alpha Solutions', 'role' => 'Manager', 'status' => 1]);
            $profiles->push((object)['business_id' => 402, 'business_name' => 'Beta Ventures', 'role' => 'Employee', 'status' => 1]);
            $profiles->push((object)['business_id' => 401, 'business_name' => 'Alpha Solutions', 'role' => 'Auditor', 'status' => 1]);
        }

        // Add one more with status 0 to ensure filtering works (mocking the status check)
        if ($userId == 40) {
            $profiles->push((object)['business_id' => 403, 'business_name' => 'Inactive Business', 'role' => 'Inactive Role', 'status' => 0]);
        }


        // Filter only active profiles (status == 1)
        return $profiles->filter(fn($p) => $p->status === 1)->values();
    }
}
