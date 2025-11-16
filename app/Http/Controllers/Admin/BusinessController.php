<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\UserType;
use App\Models\UserProfile;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class BusinessController extends Controller
{
    /**
     * Show the form for creating a new business.
     */
    public function create()
    {
        // সমস্ত অ্যাক্টিভ ইউজার এবং প্যারেন্ট বিজনেস লোড করা হচ্ছে
        $users = User::where('status', 1)->get(['id', 'name', 'phone']);
        $parentBusinesses = Business::all(['id', 'name']);

        return view('businesses.create', compact('users', 'parentBusinesses'));
    }

    /**
     * Store a newly created business and assign owner profile.
     */
    public function store(Request $request)
    {
        $ownerType = $request->input('owner_type');

        $userValidationRules = [
            'owner_type' => ['required', Rule::in(['existing', 'new'])],

            // Validation for Existing User
            'user_id' => [
                Rule::requiredIf($ownerType === 'existing'),
                'nullable',
                'exists:users,id'
            ],

            // Validation for New User (Email and Password are now optional)
            'new_user_name' => [
                Rule::requiredIf($ownerType === 'new'),
                'nullable',
                'string',
                'max:255'
            ],
            'new_user_phone' => [
                Rule::requiredIf($ownerType === 'new'),
                'nullable',
                'unique:users,phone',
                'max:20'
            ],
            'new_user_email' => ['nullable', 'email', 'unique:users,email'],
            'new_user_password' => ['nullable', 'string', 'min:6'],
        ];

        $businessValidationRules = [
            'name' => 'required|string|max:255|unique:businesses,name',
            'email' => 'nullable|email|max:255',
            'phone2' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            //'parent_business_id' => 'nullable|exists:businesses,id',
            //'is_prime' => 'boolean',
            'can_manage_roles' => 'boolean',
            //'default_owner_type_key' => 'required|exists:user_types,name', // e.g., 'rent_owner'
        ];

        // Merge and validate all rules
        $validated = $request->validate(array_merge($userValidationRules, $businessValidationRules));
        Log::info('Validated Data: ' . json_encode($validated));
        try {
        DB::beginTransaction();
        $ownerId = null;

        // 2. Handle Owner Creation/Assignment
        if ($validated['owner_type'] === 'new') {
            // Create a new User
            $owner = User::create([
                'name' => $validated['new_user_name'],
                'phone' => $validated['new_user_phone'],
                'email' => $validated['new_user_email'] ?? null,
                // Hash the password if provided, otherwise use a default/null value
                'password' => $validated['new_user_password'] ?
                    Hash::make($validated['new_user_password']) :
                    null,
            ]);
            $ownerId = $owner->id;
        } else {
            $ownerId = $validated['user_id'];
        }
        Log::info('Owner ID: ' . $ownerId);
        // 3. Create the Business
        $business = Business::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone2' => $validated['phone2'],
            'website' => $validated['website'],
            'address' => $validated['address'],
             'user_id'=> $ownerId,
            //'parent_business_id' => $validated['parent_business_id'],
            //'is_prime' => $request->has('is_prime'),
            // 'can_manage_roles' is generally stored on the business or the user's profile
            'can_manage_roles' => $request->has('can_manage_roles'),
        ]);

        // 4. Assign Owner Profile to the Business (Assuming Business model has an owner relation via UserProfile)
        $userTypes = 'admin';
        // Fetch the UserType model based on the key (e.g., 'rent_owner')
        $ownerUserType = UserType::where('name', $userTypes)->firstOrFail();

        // Create the UserProfile (Owner profile)
        UserProfile::create([
            'user_id' => $ownerId,
            'user_type_id' => $ownerUserType->id,
            'business_id' => $business->id,
            'status' => 1, // Active profile
            'default_login' => true, // Make this the primary profile
        ]);

        // Optional: You might want to unset the default_login flag for any previous profile of this user, 
        // depending on your application's single-default-profile logic.
        // Example: UserProfile::where('user_id', $ownerId)->where('id', '!=', $newProfileId)->update(['default_login' => false]);


        DB::commit();

        return redirect()->route('businesses.index')
            ->with('success', 'Business created successfully and owner assigned.');
        } catch (\Exception $e) {
        DB::rollBack();
        // Log the error for debugging
        Log::error('Business Creation Error: ' . $e->getMessage());

        return back()->withInput()
            ->with('error', 'An error occurred during business creation: ' . $e->getMessage());
        }
    }
    /* public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:businesses,name',
            'user_id' => 'required|exists:users,id',
            'email' => 'nullable|email|max:120|unique:businesses,email',
            'phone2' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:150',
            'parent_business_id' => 'nullable|exists:businesses,id',
            'is_prime' => 'boolean',
            'can_manage_roles' => 'boolean',
            // Default business owner user type key
            'default_owner_type_key' => 'required|string',
        ]);

        // Transaction ensures atomicity (Business and Profile creation)
        try {
            DB::beginTransaction();

            // 1. Create the Business
            $business = Business::create([
                'name' => $validated['name'],
                'slug' => \Str::slug($validated['name']), // Assuming you are using Laravel Str helper
                'user_id' => $validated['user_id'],
                'email' => $validated['email'],
                'phone2' => $validated['phone2'],
                'address' => $validated['address'],
                'website' => $validated['website'],
                'parent_business_id' => $validated['parent_business_id'],
                'is_prime' => $request->has('is_prime'),
                'can_manage_roles' => $request->has('can_manage_roles'),
                'default_login' => true, // Owner's business is generally default login
                'hierarchy_level_id' => $validated['parent_business_id'] ? 2 : 1, // Simple hierarchy logic
            ]);

            // 2. Assign the default profile (e.g., 'rent_owner' or 'super_admin' based on logic)
            $ownerUserType = UserType::where('name', $validated['default_owner_type_key'])->firstOrFail();

            UserProfile::create([
                'user_id' => $validated['user_id'],
                'user_type_id' => $ownerUserType->id,
                'business_id' => $business->id,
                'default_login' => true,
                'status' => 1,
            ]);

            DB::commit();

            return redirect()->route('businesses.create')->with('success', 'নতুন বিজনেস ও মালিকের প্রোফাইল সফলভাবে তৈরি করা হয়েছে!');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error and return a user-friendly message
            return back()->withInput()->with('error', 'বিজনেস তৈরি করার সময় একটি ত্রুটি হয়েছে: ' . $e->getMessage());
        }
    } */
}
