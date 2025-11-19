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
     * Display a listing of the business.
     */
    public function index(Request $request)
    {
        // Fetch search and filter parameters
        $search = $request->get('search');
        $status = $request->get('status');

        $businesses = Business::query()
            // Eager load the owner and their primary user profile
            ->with('owner.userProfile')

            // Left Join the user_profiles table to enable filtering by owner status
            ->leftJoin('user_profiles', function ($join) {
                // Match the business owner (user_id)
                $join->on('businesses.user_id', '=', 'user_profiles.user_id')
                    // And ensure we are checking the primary profile
                    ->where('user_profiles.default_login', true);
            })

            // Search logic (Business fields OR Owner fields)
            ->when($search, function ($query, $search) {
                $query->where('businesses.name', 'like', '%' . $search . '%')
                    ->orWhere('businesses.email', 'like', '%' . $search . '%')
                    ->orWhere('businesses.phone2', 'like', '%' . $search . '%')
                    ->orWhereHas('owner', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    });
            })
            // Status Filtering logic (Uses the status column from the joined user_profiles table)
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('user_profiles.status', $status);
            })

            // Select only business columns to avoid ambiguity with joined columns
            ->select('businesses.*')
            ->orderBy('businesses.created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('businesses.index', [
            'businesses' => $businesses,
            'search' => $search,
            'status' => $status,
        ]);
    }

    /**
     * Show the form for creating a new business.
     */
    public function create()
    {
        // Load existing users for the 'Existing User' dropdown
        $users = User::where('status', 1)->get(['id', 'name', 'phone']);
        $parentBusinesses = Business::all(['id', 'name']);

        return view('businesses.create', compact('users', 'parentBusinesses'));
    }


    /**
     * Store a newly created business and assign an owner.
     */
    public function store(Request $request)
    {
        // 1. Determine Owner Type from request
        $ownerType = $request->input('owner_type');

        // 2. Define Conditional Validation Rules
        $userValidationRules = [
            'owner_type' => ['required', Rule::in(['existing', 'new'])],

            // === A. EXISTING USER RULES ===
            'user_id' => [
                // Required if 'existing' is selected
                Rule::requiredIf($ownerType === 'existing'),
                // Only validate existence if 'existing' is selected
                Rule::when($ownerType === 'existing', ['exists:users,id'], ['nullable']),
            ],

            // === B. NEW USER RULES (Required if 'new' is selected) ===
            'new_user_name' => [
                Rule::requiredIf($ownerType === 'new'),
                Rule::when($ownerType === 'new', ['string', 'max:255']),
                'nullable',
            ],
            'new_user_phone' => [
                Rule::requiredIf($ownerType === 'new'),
                // Phone must be unique across all users
                Rule::when($ownerType === 'new', ['unique:users,phone', 'max:20']),
                'nullable',
            ],

            // --- Optional New User Fields ---
            'new_user_email' => ['nullable', 'email', 'unique:users,email'],
            'new_user_password' => [
                // Password is optional but must be min:6 if provided
                Rule::when($ownerType === 'new', ['string', 'min:6']),
                'nullable',
            ],
        ];

        $businessValidationRules = [
            'name' => 'required|string|max:255|unique:businesses,name',
            'email' => 'nullable|email|max:255',
            'phone2' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            // Checkbox value
            'can_manage_roles' => 'boolean',
            // Hidden field for UserType lookup
            'default_owner_type_key' => 'required|string',
        ];

        // 3. Validate the request
        $validated = $request->validate(array_merge($userValidationRules, $businessValidationRules));

        try {
            // Start Transaction to ensure both Business and Profile are created
            DB::beginTransaction();
            $ownerId = null;

            // 4. Owner Creation/Assignment 
            if ($validated['owner_type'] === 'new') {
                // Create a new User record
                $owner = User::create([
                    'name' => $validated['new_user_name'],
                    'phone' => $validated['new_user_phone'],
                    'email' => $validated['new_user_email'] ?? null,
                    // Hash password if provided, otherwise null (or random hash)
                    'password' => $validated['new_user_password'] ?
                        Hash::make($validated['new_user_password']) :
                        null,
                ]);
                $ownerId = $owner->id;
            } else {
                // Use the selected existing User ID
                $ownerId = $validated['user_id'];
            }

            // 5. Create the Business record
            $business = Business::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone2' => $validated['phone2'],
                'website' => $validated['website'],
                'address' => $validated['address'],
                'user_id' => $ownerId, // Link business to the owner
                'hierarchy_level_id' => 2, // Assuming a fixed level for new businesses
                // Check if the checkbox was present in the request
                'can_manage_roles' => $request->has('can_manage_roles'),
            ]);

            // 6. Create UserProfile (Business Owner's primary profile)
            $admin = $validated['default_owner_type_key'] ?? 'admin';
            $ownerUserType = UserType::where('name', $admin)->firstOrFail();

            UserProfile::create([
                'user_id' => $ownerId,
                'user_type_id' => $ownerUserType->id, // e.g., 'admin' role ID
                'business_id' => $business->id,
                'status' => 1, // Active
                'default_login' => true, // Primary profile for this user
            ]);

            DB::commit();

            return redirect()->route('admin.businesses.index')
                ->with('success', 'Business created successfully and owner assigned.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Business Creation Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return back()->withInput()->with('error', 'An error occurred during business creation. Please check the logs.');
        }
    }

    /**
     * Display the specified business.
     */
    public function show(Business $business)
    {
        // Eager load the Owner and their Profile data
        $business->load('owner.userProfile');

        return view('businesses.show', compact('business'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Business $business)
    {
        // Load all users for the Existing Owner selection dropdown
        $users = User::orderBy('name')->get();

        // Eager load the Owner and their Profile data
        $business->load('owner.userProfile');

        return view('businesses.edit', [
            'business' => $business,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Business $business)
    {
        // 1. Determine Owner Type
        $ownerType = $request->input('owner_type');

        // Get current Owner ID for validation ignoring/profile switching
        $currentOwnerId = $business->user_id;

        // 2. Define Conditional Validation Rules
        $userValidationRules = [
            'owner_type' => ['required', Rule::in(['existing', 'new'])],

            // === A. EXISTING USER RULES ===
            'user_id' => [
                // Required if 'existing' is selected
                Rule::requiredIf($ownerType === 'existing'),
                Rule::when($ownerType === 'existing', ['exists:users,id']),
                'nullable', // nullable if 'new' is selected
            ],

            // === B. NEW USER RULES ===
            'new_user_name' => [
                Rule::requiredIf($ownerType === 'new'),
                'nullable',
                'string',
                'max:255',
            ],
            'new_user_phone' => [
                Rule::requiredIf($ownerType === 'new'),
                'nullable',
                'max:20',
                // Unique check: Ignore the current business owner's phone to allow updates without error
                Rule::unique('users', 'phone')->ignore($currentOwnerId),
            ],
            'new_user_email' => [
                'nullable',
                'email',
                'max:255',
                // Unique check: Ignore the current business owner's email
                Rule::unique('users', 'email')->ignore($currentOwnerId),
            ],
            // Password is optional, must be min:6 if provided.
            'new_user_password' => [
                Rule::when($ownerType === 'new', ['string', 'min:6']),
                'nullable',
            ],
        ];

        $businessValidationRules = [
            'name' => [
                'required',
                'string',
                'max:255',
                // Unique check: Ignore the current business's name
                Rule::unique('businesses', 'name')->ignore($business->id),
            ],
            'email' => 'nullable|email|max:255',
            'phone2' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            // Checkbox
            'can_manage_roles' => 'nullable|boolean',
            'default_owner_type_key' => 'required|string',
        ];

        // 3. Merge and Validate the request
        $validated = $request->validate(array_merge($userValidationRules, $businessValidationRules));

        try {
            DB::beginTransaction();
            $ownerId = $currentOwnerId;

            // 4. Owner Assignment Logic (Creation or Selection)
            if ($validated['owner_type'] === 'new') {
                // Logic assumes 'new' means creating an entirely new user
                $newUserData = [
                    'name' => $validated['new_user_name'],
                    'phone' => $validated['new_user_phone'],
                    'email' => $validated['new_user_email'] ?? null,
                    // If no password is given, create a random hash for security
                    'password' => $validated['new_user_password'] ?
                        Hash::make($validated['new_user_password']) :
                        Hash::make(substr(md5(rand()), 0, 8)),
                ];

                $owner = User::create($newUserData);
                $ownerId = $owner->id;
            } elseif ($validated['owner_type'] === 'existing') {
                // Use the selected existing User ID
                $ownerId = $validated['user_id'];
                // NOTE: User details (name/phone/email/password) of the existing user are NOT updated here.
            }

            // 5. Update Business Details
            $business->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone2' => $validated['phone2'],
                'website' => $validated['website'],
                'address' => $validated['address'],
                'user_id' => $ownerId, // Set the new/current Owner ID
                'can_manage_roles' => $request->has('can_manage_roles'),
            ]);

            // 6. UserProfile Logic (Only if Owner changes)
            if ($ownerId != $currentOwnerId) {
                // Deactivate/un-default the OLD Owner's profile for this business
                UserProfile::where('user_id', $currentOwnerId)
                    ->where('business_id', $business->id)
                    ->update(['default_login' => false, 'status' => 0]);

                // Find the appropriate UserType (e.g., 'admin')
                $ownerUserType = UserType::where('name', $validated['default_owner_type_key'])->firstOrFail();

                // Create or Update the NEW Owner's profile for this business
                $newProfile = UserProfile::firstOrNew([
                    'user_id' => $ownerId,
                    'business_id' => $business->id,
                ]);

                $newProfile->fill([
                    'user_type_id' => $ownerUserType->id,
                    'status' => 1,
                    'default_login' => true, // Set as primary profile
                ])->save();
            }

            DB::commit();

            return redirect()->route('admin.businesses.index')
                ->with('success', 'Business **' . $business->name . '** updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging
            Log::error('Business Update Failed.', ['business_id' => $business->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            // Return with error message and preserved input
            return back()->withInput()->with('error', 'Business update failed. Internal error: ' . $e->getMessage());
        }
    }

}
