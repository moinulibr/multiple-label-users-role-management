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
    public function index(Request $request)
    {
        // সার্চ এবং ফিল্টার লজিক
        $search = $request->get('search');
        $status = $request->get('status');

        $businesses = Business::query()
            // ✅ এখন owner.userProfile রিলেশনটি কাজ করবে!
            ->with('owner.userProfile')

            // Filtering-এর জন্য Join ব্যবহার
            ->leftJoin('user_profiles', function ($join) {
                // UserProfile-এ user_id এবং Business owner-এর user_id ম্যাচ করে
                $join->on('businesses.user_id', '=', 'user_profiles.user_id')
                    // এবং নিশ্চিত করা হলো এটি primary profile
                    ->where('user_profiles.default_login', true);
            })

            // সার্চ লজিক
            ->when($search, function ($query, $search) {
                $query->where('businesses.name', 'like', '%' . $search . '%')
                    ->orWhere('businesses.email', 'like', '%' . $search . '%')
                    ->orWhere('businesses.phone2', 'like', '%' . $search . '%')
                    ->orWhereHas('owner', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    });
            })
            // Status Filtering লজিক (UserProfile-এর status কলাম ব্যবহার করে)
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                // status-টি user_profiles টেবিল থেকে আসছে
                $query->where('user_profiles.status', $status);
            })

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

    public function create()
    {
        $users = User::where('status', 1)->get(['id', 'name', 'phone']);
        $parentBusinesses = Business::all(['id', 'name']);

        return view('businesses.create', compact('users', 'parentBusinesses'));
    }


    /**
     * Store a newly created business and assign an owner.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 1. Owner Type Fetch করা
        $ownerType = $request->input('owner_type');

        // 2. Validation Rules
        $userValidationRules = [
            'owner_type' => ['required', Rule::in(['existing', 'new'])],

            // === A. EXISTING USER RULS ===
            'user_id' => [
                Rule::requiredIf($ownerType === 'existing'),
                 Rule::when($ownerType === 'existing', ['exists:users,id'], ['nullable']),
            ],

            // === B. NEW USER RULS ===
            'new_user_name' => [
                Rule::requiredIf($ownerType === 'new'),

                Rule::when($ownerType === 'new', ['string', 'max:255']),
                'nullable',
            ],
            'new_user_phone' => [
                Rule::requiredIf($ownerType === 'new'),
                Rule::when($ownerType === 'new', ['unique:users,phone', 'max:20']),
                'nullable',
            ],

            // --- Optional New User Fields ---
            'new_user_email' => ['nullable', 'email', 'unique:users,email'],

            // === B. NEW USER RULS ===
            'new_user_password' => [
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
            'can_manage_roles' => 'boolean',
            'default_owner_type_key' => 'required|string', // Hidden field check
        ];

        $validated = $request->validate(array_merge($userValidationRules, $businessValidationRules));

        try {
            DB::beginTransaction();
            $ownerId = null;

            // ৪. Owner Creation/Assignment 
            if ($validated['owner_type'] === 'new') {
                $owner = User::create([
                    'name' => $validated['new_user_name'],
                    'phone' => $validated['new_user_phone'],
                    'email' => $validated['new_user_email'] ?? null,
                    'password' => $validated['new_user_password'] ?
                        Hash::make($validated['new_user_password']) :
                        null, 
                ]);
                $ownerId = $owner->id;
            } else {
                $ownerId = $validated['user_id'];
            }

            $business = Business::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone2' => $validated['phone2'],
                'website' => $validated['website'],
                'address' => $validated['address'],
                'user_id' => $ownerId, 
                'hierarchy_level_id' => 2,
                'can_manage_roles' => $request->has('can_manage_roles'),
            ]);

            // 6. UserProfile (Business Owner)
            $admin = $validated['default_owner_type_key'] ?? 'admin';
            //$ownerUserType = UserType::where('name', $validated['default_owner_type_key'])->firstOrFail();
            $ownerUserType = UserType::where('name', $admin)->firstOrFail();

            UserProfile::create([
                'user_id' => $ownerId,
                'user_type_id' => $ownerUserType->id,//admin == 2
                'business_id' => $business->id,
                'status' => 1,
                'default_login' => true,
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

    public function show(Business $business)
    {
        // Owner এবং তার Profile ডেটা eager load করা হলো
        $business->load('owner.userProfile');

        return view('businesses.show', compact('business'));
    }

    // --- ২. এডিট ফর্ম লোড (edit) ---
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Business $business)
    {
        // সমস্ত ইউজারদের তালিকা লোড করা হলো Existing Owner সিলেক্ট করার জন্য
        $users = User::orderBy('name')->get();

        // Owner এবং তার Profile ডেটা eager load করা হলো
        $business->load('owner.userProfile');

        return view('businesses.edit', [
            'business' => $business,
            'users' => $users,
        ]);
    }

    // --- ৩. ডেটা আপডেট (update) ---
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Business $business)
    {
        // ১. Owner Type Fetch করা
        $ownerType = $request->input('owner_type');

        // ২. ভ্যালিডেশন রুলস তৈরি করা
        // বর্তমান Owner এর ID বের করা
        $currentOwnerId = $business->user_id;

        $userValidationRules = [
            'owner_type' => ['required', Rule::in(['existing', 'new'])],

            // === A. EXISTING USER RULS ===
            'user_id' => [
                // 'existing' সিলেক্ট করা হলে user_id আবশ্যক
                Rule::requiredIf($ownerType === 'existing'),
                Rule::when($ownerType === 'existing', ['exists:users,id']),
                'nullable', // 'new' সিলেক্ট করা হলে user_id nullable হবে
            ],

            // === B. NEW USER RULS ===
            'new_user_name' => [
                // 'new' সিলেক্ট করা হলে Name আবশ্যক
                Rule::requiredIf($ownerType === 'new'),
                'nullable',
                'string',
                'max:255',
            ],
            'new_user_phone' => [
                // 'new' সিলেক্ট করা হলে Phone আবশ্যক
                Rule::requiredIf($ownerType === 'new'),
                'nullable',
                'max:20',
                // ফোন ইউনিক হতে হবে। যদি Owner পরিবর্তন না হয়, তবে এই রুল ইগনোর করবে না। 
                // তাই, বর্তমান Owner-কে বাদ দিয়ে বাকিদের সাথে চেক করবে।
                Rule::unique('users', 'phone')->ignore($currentOwnerId),
            ],
            'new_user_email' => [
                'nullable',
                'email',
                'max:255',
                // ইমেল ইউনিক হতে হবে। বর্তমান Owner-কে বাদ দিয়ে বাকিদের সাথে চেক করবে।
                Rule::unique('users', 'email')->ignore($currentOwnerId),
            ],
            // পাসওয়ার্ড ঐচ্ছিক, যদি দেওয়া হয় তবে min:6 হতে হবে।
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
                // Business Name: এটি বর্তমান business-এর নাম ছাড়া ইউনিক হতে হবে।
                Rule::unique('businesses', 'name')->ignore($business->id),
            ],
            'email' => 'nullable|email|max:255',
            'phone2' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            // Checkbox এর জন্য boolean এবং default value সেট করা হয়েছে।
            'can_manage_roles' => 'nullable|boolean',
            'default_owner_type_key' => 'required|string',
        ];

        // ৩. সমস্ত রুলস একত্রীকরণ এবং ভ্যালিডেশন
        // ভ্যালিডেশন ফেইল হলে Laravel নিজে থেকেই এরর সহ back করে দেবে।
        $validated = $request->validate(array_merge($userValidationRules, $businessValidationRules));

        try {
            DB::beginTransaction();
            $ownerId = $currentOwnerId;

            // ৪. Owner Assignment লজিক
            if ($validated['owner_type'] === 'new') {
                // নতুন ইউজার তৈরি 

                // নতুন ইউজারের তথ্য তৈরি
                $newUserData = [
                    'name' => $validated['new_user_name'],
                    'phone' => $validated['new_user_phone'],
                    'email' => $validated['new_user_email'] ?? null,
                    // পাসওয়ার্ড না দিলে একটি random পাসওয়ার্ড তৈরি করা হলো
                    'password' => $validated['new_user_password'] ?
                        Hash::make($validated['new_user_password']) :
                        Hash::make(substr(md5(rand()), 0, 8)),
                ];

                // যদি বর্তমানে কোনো user না থাকে (যা হওয়ার কথা নয়), তবে নতুন user তৈরি করা সহজ।
                // কিন্তু যেহেতু এটি একটি Update মেথড, আমরা বর্তমান Owner-কে আপডেট করতে পারি
                // যদি নতুন ফোন/নাম/ইমেল দেওয়া থাকে। 
                // তবে 'new' সিলেক্ট করা মানে সম্পূর্ণ নতুন ইউজার তৈরি করা, তাই সেটাই করা হলো:
                $owner = User::create($newUserData);
                $ownerId = $owner->id;
            } elseif ($validated['owner_type'] === 'existing') {
                // Existing ইউজার ব্যবহার করা
                $ownerId = $validated['user_id'];

                // এখানে কোনো নতুন ইউজার তৈরি হচ্ছে না।
            }

            // ৫. Business আপডেট
            $business->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone2' => $validated['phone2'],
                'website' => $validated['website'],
                'address' => $validated['address'],
                'user_id' => $ownerId, // নতুন/বর্তমান Owner ID সেট করা হলো
                'can_manage_roles' => $request->has('can_manage_roles'),
                // 'is_prime' এর মতো অন্যান্য field থাকলে এখানে যোগ করুন
            ]);

            // ৬. UserProfile লজিক (যদি Owner পরিবর্তন হয়)
            if ($ownerId != $currentOwnerId) {
                // পুরানো Owner-এর UserProfileinactive/default_login false করা
                UserProfile::where('user_id', $currentOwnerId)
                    ->where('business_id', $business->id)
                    ->update(['default_login' => false, 'status' => 0]);

                // নতুন Owner-এর জন্য UserProfile তৈরি/আপডেট করা
                $ownerUserType = UserType::where('name', $validated['default_owner_type_key'])->firstOrFail();

                // নতুন Owner-এর জন্য নতুন প্রোফাইল তৈরি বা existing আপডেট করা 
                $newProfile = UserProfile::firstOrNew([
                    'user_id' => $ownerId,
                    'business_id' => $business->id,
                ]);

                $newProfile->fill([
                    'user_type_id' => $ownerUserType->id,
                    'status' => 1,
                    'default_login' => true,
                ])->save();
            }

            DB::commit();

            return redirect()->route('admin.businesses.index')
                ->with('success', 'Business **' . $business->name . '** updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            // এরর লগ করা হলো
            Log::error('Business Update Failed.', ['business_id' => $business->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            // এরর মেসেজ সহ back করা হলো
            return back()->withInput()->with('error', 'Business update failed. Internal error: ' . $e->getMessage());
        }
    }
    /**
     * Store a newly created business and assign owner profile.
     */
    public function oldstore(Request $request)
    {
        $ownerType = $request->input('owner_type');

        $userValidationRules = [
            'owner_type' => ['required', Rule::in(['existing', 'new'])],

            // === EXISTING USER RULS (Fixed in previous step) ===
            'user_id' => [
                Rule::requiredIf($ownerType === 'existing'),
                // 'nullable' removed
                'exists:users,id'
            ],

            // === NEW USER RULS (Fixed now) ===
            'new_user_name' => [
                Rule::requiredIf($ownerType === 'new'),
                // 'nullable' removed - If required, it must not be null/empty
                'string',
                'max:255'
            ],
            'new_user_phone' => [
                Rule::requiredIf($ownerType === 'new'),
                // 'nullable' removed - If required, it must not be null/empty
                'unique:users,phone',
                'max:20'
            ],

            // --- Optional Fields (nullable is appropriate here) ---
            'new_user_email' => ['nullable', 'email', 'unique:users,email'],
            'new_user_password' => ['nullable', 'string', 'min:6'],
        ];

        $businessValidationRules = [
            'name' => 'required|string|max:255|unique:businesses,name',
            'email' => 'nullable|email|max:255',
            'phone2' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            'can_manage_roles' => 'boolean',
            //'parent_business_id' => 'nullable|exists:businesses,id',
            //'is_prime' => 'boolean',
            //'default_owner_type_key' => 'required|exists:user_types,name', // e.g., 'rent_owner'
        ];

        // Merge and validate all rules
        $validated = $request->validate(array_merge($userValidationRules, $businessValidationRules));

        //try {
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
               // 'password' => $request->input('new_user_password') ? Hash::make($request->input('new_user_password')) : null,
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
            'hierarchy_level_id'=> 2,
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


        //DB::commit();

        return redirect()->route('admin.businesses.index')
            ->with('success', 'Business created successfully and owner assigned.');
        //} catch (\Exception $e) {
        //DB::rollBack();
        // Log the error for debugging
       // Log::error('Business Creation Error: ' . $e->getMessage());

        //return back()->withInput()->with('error', 'An error occurred during business creation: ' . $e->getMessage());
        //}
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
