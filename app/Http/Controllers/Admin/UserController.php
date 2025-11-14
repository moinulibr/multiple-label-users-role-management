<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserType;
use App\Models\Business;
use App\Models\Role;
use App\Models\UserProfile;
use App\Services\UserContextManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Index method (unchanged from your primary provided version, excluding commented block)
     */
    public function index(Request $request)
    {
        $context = app(UserContextManager::class);
        $businessId = $context->getBusinessId();
        $profile = $context->getCurrentProfile();
        $userType = $context->getUserType();
        $userTypeId = $context->getUserTypeId();

        $superAdminType = config('app_permissions.fixedUserType')[1];
        $adminType = config('app_permissions.fixedUserType')[2];

        $userTypes = UserType::where('status', 1)
            ->whereIn('name', [$superAdminType, $adminType])
            ->get()
            ->keyBy('name');

        $superAdminTypeId = $userTypes[$superAdminType]->id ?? null;
        $adminTypeId = $userTypes[$adminType]->id ?? null;

        $isPrimeCompany = $profile->business?->is_prime ?? false;

        $queryProfiles = UserProfile::select('user_id', 'business_id', 'user_type_id')
            ->where('status', true);

        if ($isPrimeCompany) {
            if ($userType === $superAdminType) {
                $queryProfiles->where('user_id', '!=', auth()->id());
            } elseif ($userType === $adminType) {
                $queryProfiles->where('user_type_id', '!=', $superAdminTypeId);
            }
        } else {
            $queryProfiles->where('business_id', $businessId)
                ->where('user_type_id', '!=', $adminTypeId);
        }

        $allowsUserIds = $queryProfiles->distinct()->pluck('user_id')->toArray();

        if (empty($allowsUserIds)) {
            return view('cdbc.users.index', ['users' => collect()]);
        }

        $users = User::select(['id', 'name', 'email', 'phone', 'status'])
            ->with([
                'profiles.business:id,name',
                'profiles.userType:id,display_name',
                'profiles.roles:id,display_name'
            ])
            ->whereIn('id', $allowsUserIds)
            ->when(
                $request->search,
                fn($q, $v) =>
                $q->where(
                    fn($sub) =>
                    $sub->where('name', 'like', "%$v%")
                        ->orWhere('email', 'like', "%$v%")
                        ->orWhere('phone', 'like', "%$v%")
                )
            )
            ->when(
                $request->filled('status'),
                fn($q) => $q->where('status', $request->status)
            )
            ->when($request->business_id, function ($q, $v) {
                $q->whereHas('profiles.business', fn($sub) => $sub->where('business_id', $v));
            })
            ->when($request->user_type_id, function ($q, $v) {
                $q->whereHas('profiles.userType', fn($sub) => $sub->where('user_type_id', $v));
            })
            ->when($request->role_id, function ($q, $v) {
                $q->whereHas('profiles.roles', fn($sub) => $sub->where('roles.id', $v));
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->appends($request->query());

        // For filter dropdowns
        $businesses = Business::select('id', 'name')->get();
        $userTypes = UserType::select('id', 'display_name')->where('status', 1)->get();
        $roles = Role::select('id', 'display_name', 'name')->get();

        return view('cdbc.users.index', compact('users', 'businesses', 'userTypes', 'roles'));
    }

    /**
     * Modified create method to determine user context and load data accordingly.
     */
    public function create()
    {
        $context = app(UserContextManager::class);
        $profile = $context->getCurrentProfile();

        $isPrimeCompany = $profile->business?->is_prime ?? false;
        $currentRoles = $profile->roles ?? [];
        $superAdminType = config('app_permissions.fixedUserType')[1];
        $adminType = config('app_permissions.fixedUserType')[2];

        $userTypes = UserType::where('status', 1)
            ->whereNotIn('name', [$superAdminType, $adminType])
            ->where('dashboard_key', $adminType)
            ->get();

        // Determine if the logged-in user is an employee of the Software Owner Company (is_prime = true)
        $isSoftwareOwnerEmployee = $profile->business?->is_prime ?? false;

        $businesses = collect();
        $currentBusinessName = $profile->business?->name ?? 'N/A';
        $currentBusinessId = $profile->business?->id ?? null;

        if ($isSoftwareOwnerEmployee) {
            // Software Owner Employee needs all businesses for the dropdown IF they choose 'Another Business'
            $businesses = Business::where('status', 1)->get();
        }

        // Check if the current user has a business assigned (should always be true for admins/employees)
        $hasBusinessAssigned = !empty($currentBusinessId);
        
        $roles = Role::where('status', 1)->where('business_id', $currentBusinessId)->get();

        return view('cdbc.users.create', compact(
            'userTypes',
            'businesses', // All businesses (for prime company user), or empty (for tenant user)
            'isSoftwareOwnerEmployee',
            'currentBusinessName',
            'currentBusinessId',
            'hasBusinessAssigned',
            'roles',
            'isPrimeCompany',
            'currentRoles',
            'profile'
        ));
    }

    /**
     * Modified store method to enforce business_id assignment based on user context.
     */
    /*public function store(Request $request)
    {
        return $request->role_id ?? '';
        return $request->role_id?? 'nai';
        $request->validate([
            'name' => 'required',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'password' => 'required|min:6',
            'profiles.*.user_type_id' => 'required',
            // Note: Validation for business_id presence is handled by logic below based on context
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'secondary_phone' => $request->secondary_phone,
            'password' => bcrypt($request->password),
            'status' => $request->status ?? 1,
            'is_developer' => $request->is_developer ?? 0,
        ]);

        // Get context for assignment logic
        $context = app(UserContextManager::class);
        $currentProfile = $context->getCurrentProfile();
        $isSoftwareOwnerEmployee = $currentProfile->business?->is_prime ?? false;
        $currentBusinessId = $currentProfile->business?->id ?? null;

        if ($request->profiles) {
            foreach ($request->profiles as $p) {
                $businessIdToAssign = $p['business_id'] ?? null;

                if (!$isSoftwareOwnerEmployee) {
                    // Tenant Employee: Must assign to their own business, ignoring any form input
                    $businessIdToAssign = $currentBusinessId;

                    // Add a safety check for the current user's business ID
                    if (is_null($businessIdToAssign)) {
                        Log::error('Attempt to create user by Tenant employee without a current business ID.');
                        continue; // Skip creating profile if business ID is missing for a tenant
                    }
                } elseif ($isSoftwareOwnerEmployee && $request->input('create_for_own_business')) {
                    // Software Owner Employee checked 'Own Business', so force their business ID
                    $businessIdToAssign = $currentBusinessId;
                }

                // If isSoftwareOwnerEmployee is true and create_for_own_business is false, 
                // $businessIdToAssign should come directly from $p['business_id'] (the dropdown), which is correct.
                if($request->role_id){
                   
                }

                $user->profiles()->create([
                    'user_type_id' => $p['user_type_id'],
                    'business_id' => $businessIdToAssign,
                    'default_login' => isset($p['default_login']) && $p['default_login'] ? 1 : 0,
                    'status' => 1
                ]);
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    } */
    public function store(Request $request)
    {
        // 1. Get context for validation and assignment logic
        $context = app(UserContextManager::class);
        $currentProfile = $context->getCurrentProfile();
        $currentBusinessId = $currentProfile->business?->id ?? null;
        $isSoftwareOwnerEmployee = $currentProfile->business?->is_prime ?? false;
        $isCreatingForOwnBusiness = $request->input('create_for_own_business', '1') === '1'; // Default to '1' if field is missing/not applicable

        // 2. Base Validation
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'secondary_phone' => 'nullable|string',
            'password' => 'required|min:6',
            'status' => 'nullable|in:0,1,2',
            'role_id' => [
                'nullable',
                Rule::requiredIf(fn() => $isSoftwareOwnerEmployee && $isCreatingForOwnBusiness), // Role is required only if creating for own business
                'integer',
                'exists:roles,id' // Adjust table name if necessary
            ],
            'profiles' => ['required', 'array', 'min:1'],
            'profiles.*.user_type_id' => ['required', 'integer', 'exists:user_types,id'],
            'profiles.*.default_login' => ['nullable', 'boolean'],
        ];

        // 3. Conditional Business ID Validation for Prime User
        if ($isSoftwareOwnerEmployee && !$isCreatingForOwnBusiness) {
            // If creating for 'Another Business', the business_id must be present in the profiles array from the dropdown.
            $rules['profiles.*.business_id'] = ['required', 'integer', 'exists:businesses,id', 'different:currentBusinessId'];
        }

        $validatedData = $request->validate($rules);

        // 4. Custom Validation for Duplicates & Single Default Login
        $profiles = collect($validatedData['profiles']);

        // Check 4.1: Profile Duplication (user_type_id + business_id combination)
        $profiles->each(function ($p, $index) use ($profiles, $currentBusinessId, $isSoftwareOwnerEmployee, $isCreatingForOwnBusiness) {
            $businessId = $p['business_id'] ?? null;

            // Determine the final business ID based on creation context
            if (!$isSoftwareOwnerEmployee || ($isSoftwareOwnerEmployee && $isCreatingForOwnBusiness)) {
                $businessId = $currentBusinessId;
            }

            // Check for duplicates
            $duplicate = $profiles->filter(function ($dp, $dpIndex) use ($p, $businessId, $index) {
                // Check against other items in the array
                return $dpIndex !== $index &&
                    $dp['user_type_id'] == $p['user_type_id'] &&
                    ($dp['business_id'] ?? null) == $businessId;
            })->isNotEmpty();

            if ($duplicate) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "profiles.$index.duplicate" => 'একই ইউজার টাইপ এবং বিজনেসের প্রোফাইল ডুপ্লিকেট করা হয়েছে।',
                ]);
            }
        });

        // Check 4.2: Single Default Login
        $defaultLoginCount = $profiles->filter(fn($p) => isset($p['default_login']) && $p['default_login'])->count();
        if ($defaultLoginCount !== 1) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'profiles' => 'ঠিক একটি প্রোফাইলকে ডিফল্ট লগইন হিসেবে নির্বাচন করতে হবে।',
            ]);
        }


        // 5. User Creation and Profile Assignment
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'] ?? null,
            'phone' => $validatedData['phone'],
            'secondary_phone' => $validatedData['secondary_phone'] ?? null,
            'password' => bcrypt($validatedData['password']),
            'status' => $validatedData['status'] ?? 1,
            'is_developer' => $request->is_developer ?? 0,
        ]);

        foreach ($profiles as $p) {
            $businessIdToAssign = $p['business_id'] ?? null;

            if (!$isSoftwareOwnerEmployee || ($isSoftwareOwnerEmployee && $isCreatingForOwnBusiness)) {
                // Tenant or Prime for Own Business: Force current business ID
                $businessIdToAssign = $currentBusinessId;
            }

            // Final check for business ID presence before creation
            if (is_null($businessIdToAssign)) {
                Log::error('Missing business ID during user profile creation.', ['user_id' => $user->id, 'profile_data' => $p]);
                continue;
            }

            $user->profiles()->create([
                'user_type_id' => $p['user_type_id'],
                'business_id' => $businessIdToAssign,
                'default_login' => isset($p['default_login']) && $p['default_login'] ? 1 : 0,
                'status' => 1
            ]);
        }

        // 6. Assign Role if applicable (Only for Own Business by Prime Employee)
        if ($isSoftwareOwnerEmployee && $isCreatingForOwnBusiness && $request->role_id) {
            // Assuming you have a method to sync roles, e.g., using Spatie Permission package or similar
            $user->assignRole($request->role_id);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    /**
     * Edit, Update, Show, Destroy methods remain the same as provided.
     */
    public function edit(User $user)
    {
        // Replicating create() logic for edit to pass context
        $context = app(UserContextManager::class);
        $profile = $context->getCurrentProfile();
        $isPrimeCompany = $profile->business?->is_prime ?? false;

        $superAdminType = config('app_permissions.fixedUserType')[1];
        $adminType = config('app_permissions.fixedUserType')[2];
    
        $userTypes = UserType::where('status', 1)
            ->whereNotIn('name', [$superAdminType, $adminType])
            ->where('dashboard_key', $adminType)
            ->get();

        $isSoftwareOwnerEmployee = $profile->business?->is_prime ?? false;
        $businesses = collect();
        if ($isSoftwareOwnerEmployee) {
            $businesses = Business::where('status', 1)->get();
        }

        $currentBusinessName = $profile->business?->name ?? 'N/A';
        $currentBusinessId = $profile->business?->id ?? null;
        $hasBusinessAssigned = !empty($currentBusinessId);
        $roles = Role::where('status', 1)->where('business_id', $currentBusinessId)->get();
        return $user->profiles;
        $currentSelectedRoleId = $user->profiles()->with('roles')->where('business_id', 1)->first()?->roles->first()?->id;

        return view('cdbc.users.create', compact(
            'user',
            'userTypes',
            'businesses',
            'isSoftwareOwnerEmployee',
            'currentBusinessName',
            'currentBusinessId',
            'hasBusinessAssigned',
            'roles',
            'isPrimeCompany',
            'currentSelectedRoleId',
            'profile'
        ));
    }

    public function update(Request $request, User $user)
    {
        // 1. Get context for validation and assignment logic
        $context = app(UserContextManager::class);
        $currentProfile = $context->getCurrentProfile();
        $currentBusinessId = $currentProfile->business?->id ?? null;
        $isSoftwareOwnerEmployee = $currentProfile->business?->is_prime ?? false;
        // For Update, we check if the user had another business profile to determine the radio button state
        $hasAnotherBusinessProfile = $user->profiles->contains(fn($profile) => $profile->business_id != $currentBusinessId);
        $isCreatingForOwnBusiness = $request->input('create_for_own_business', $hasAnotherBusinessProfile ? '0' : '1') === '1';


        // 2. Base Validation
        $rules = [
            'name' => 'required|string|max:255',
            'email' => "nullable|email|unique:users,email,$user->id",
            'phone' => "required|string|unique:users,phone,$user->id",
            'secondary_phone' => 'nullable|string',
            'password' => 'nullable|min:6', // Password is not required for update
            'status' => 'nullable|in:0,1,2',
            'role_id' => [
                'nullable',
                Rule::requiredIf(fn() => $isSoftwareOwnerEmployee && $isCreatingForOwnBusiness),
                'integer',
                'exists:roles,id'
            ],
            'profiles' => ['required', 'array', 'min:1'],
            'profiles.*.user_type_id' => ['required', 'integer', 'exists:user_types,id'],
            'profiles.*.default_login' => ['nullable', 'boolean'],
        ];

        // 3. Conditional Business ID Validation for Prime User
        if ($isSoftwareOwnerEmployee && !$isCreatingForOwnBusiness) {
            $rules['profiles.*.business_id'] = ['required', 'integer', 'exists:businesses,id'];
        }

        $validatedData = $request->validate($rules);

        // 4. Custom Validation for Duplicates & Single Default Login
        $profiles = collect($validatedData['profiles']);

        // Check 4.1: Profile Duplication (user_type_id + business_id combination)
        $profiles->each(function ($p, $index) use ($profiles, $currentBusinessId, $isSoftwareOwnerEmployee, $isCreatingForOwnBusiness) {
            $businessId = $p['business_id'] ?? null;

            // Determine the final business ID based on creation context
            if (!$isSoftwareOwnerEmployee || ($isSoftwareOwnerEmployee && $isCreatingForOwnBusiness)) {
                $businessId = $currentBusinessId;
            }

            // Check for duplicates
            $duplicate = $profiles->filter(function ($dp, $dpIndex) use ($p, $businessId, $index) {
                return $dpIndex !== $index &&
                    $dp['user_type_id'] == $p['user_type_id'] &&
                    ($dp['business_id'] ?? null) == $businessId;
            })->isNotEmpty();

            if ($duplicate) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "profiles.$index.duplicate" => 'একই ইউজার টাইপ এবং বিজনেসের প্রোফাইল ডুপ্লিকেট করা হয়েছে।',
                ]);
            }
        });

        // Check 4.2: Single Default Login
        $defaultLoginCount = $profiles->filter(fn($p) => isset($p['default_login']) && $p['default_login'])->count();
        if ($defaultLoginCount !== 1) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'profiles' => 'ঠিক একটি প্রোফাইলকে ডিফল্ট লগইন হিসেবে নির্বাচন করতে হবে।',
            ]);
        }


        // 5. User Update and Profile Sync
        $user->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'] ?? null,
            'phone' => $validatedData['phone'],
            'secondary_phone' => $validatedData['secondary_phone'] ?? null,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'status' => $validatedData['status'] ?? 1,
            'is_developer' => $request->is_developer ?? 0,
        ]);

        // Delete existing profiles and create new ones
        $user->profiles()->delete();

        foreach ($profiles as $p) {
            $businessIdToAssign = $p['business_id'] ?? null;

            if (!$isSoftwareOwnerEmployee || ($isSoftwareOwnerEmployee && $isCreatingForOwnBusiness)) {
                // Tenant or Prime for Own Business: Force current business ID
                $businessIdToAssign = $currentBusinessId;
            }

            if (is_null($businessIdToAssign)) {
                Log::error('Missing business ID during user profile update.', ['user_id' => $user->id, 'profile_data' => $p]);
                continue;
            }

            $user->profiles()->create([
                'user_type_id' => $p['user_type_id'],
                'business_id' => $businessIdToAssign,
                'default_login' => isset($p['default_login']) && $p['default_login'] ? 1 : 0,
                'status' => 1
            ]);
        }

        // 6. Sync Role if applicable (Only for Own Business by Prime Employee)
        if ($isSoftwareOwnerEmployee && $isCreatingForOwnBusiness && $request->role_id) {
            $user->syncRoles([$request->role_id]);
        } else {
            // Remove roles if switched to 'Another Business' or if role selection is empty
            $user->syncRoles([]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }


    /* public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => "nullable|email|unique:users,email,$user->id",
            'phone' => "required|unique:users,phone,$user->id",
            'profiles.*.user_type_id' => 'required',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'secondary_phone' => $request->secondary_phone,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'status' => $request->status ?? 1,
            'is_developer' => $request->is_developer ?? 0,
        ]);

        // Get context for assignment logic
        $context = app(UserContextManager::class);
        $currentProfile = $context->getCurrentProfile();
        $isSoftwareOwnerEmployee = $currentProfile->business?->is_prime ?? false;
        $currentBusinessId = $currentProfile->business?->id ?? null;

        $user->profiles()->delete();

        if ($request->profiles) {
            foreach ($request->profiles as $p) {
                $businessIdToAssign = $p['business_id'] ?? null;

                if (!$isSoftwareOwnerEmployee) {
                    // Tenant Employee: Must assign to their own business, ignoring any form input
                    $businessIdToAssign = $currentBusinessId;

                    if (is_null($businessIdToAssign)) {
                        Log::error('Attempt to update user by Tenant employee without a current business ID.');
                        continue;
                    }
                } elseif ($isSoftwareOwnerEmployee && $request->input('create_for_own_business')) {
                    // Software Owner Employee checked 'Own Business'
                    $businessIdToAssign = $currentBusinessId;
                }

                $user->profiles()->create([
                    'user_type_id' => $p['user_type_id'],
                    'business_id' => $businessIdToAssign,
                    'default_login' => isset($p['default_login']) && $p['default_login'] ? 1 : 0,
                    'status' => 1
                ]);
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    } */

    public function show(User $user)
    {
        return view('cdbc.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        $user->update(['status' => 0]);
        return redirect()->route('admin.users.index')->with('success', 'User soft deleted!');
    }
}
