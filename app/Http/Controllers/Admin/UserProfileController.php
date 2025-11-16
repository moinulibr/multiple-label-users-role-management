<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserType;
use App\Models\Business;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserProfileController extends Controller
{
    /**
     * Show all profiles for a specific user.
     */
    public function index(User $user)
    {
        $userProfiles = $user->profiles()->with(['userType', 'business', 'roles'])->get();
        // UI-তে দৃশ্যমান user_types লোড করা হচ্ছে
        $userTypes = UserType::where('visiblity', true)->get(['id', 'display_name', 'name']);
        $businesses = Business::all(['id', 'name']);
        $users = User::all();
        return view('users.profiles.index', compact('users','user', 'userProfiles', 'userTypes', 'businesses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $request->all();
        // 1. Determine Validation Rules based on Owner Type
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
            //DB::beginTransaction();
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

            return redirect()->route('businesses.create')
                ->with('success', 'Business created successfully and owner assigned.');
        } catch (\Exception $e) {
            DB::rollBack();
            //Log the error for debugging
            Log::error('Business Creation Error: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'An error occurred during business creation: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user profile.
     */
    public function destroy(User $user, UserProfile $profile)
    {
        if ($profile->user_id !== $user->id) {
            return back()->with('error', 'প্রোফাইলটি এই ইউজারের নয়।');
        }

        // Prevent deleting the *last* profile, if required by business logic
        if ($user->profiles()->count() <= 1) {
            return back()->with('error', 'ইউজারের শেষ প্রোফাইলটি ডিলিট করা যাবে না।');
        }

        $profile->delete();

        return back()->with('success', 'প্রোফাইল সফলভাবে রিমুভ করা হয়েছে।');
    }

    // You might add an API/utility method here to fetch roles based on business_id for AJAX.
}
