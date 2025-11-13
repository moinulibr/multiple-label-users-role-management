<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserType;
use App\Models\Business;
use App\Models\UserProfile;
use App\Services\UserContextManager;

class UserController extends Controller
{
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

        $queryProfiles = UserProfile::select('user_id', 'business_id', 'user_type_id')->where('status', true);

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
            ->when($request->search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%")
                        ->orWhere('phone', 'like', "%$search%");
                });
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('cdbc.users.index', compact('users'));
    }

    /* public function index(Request $request)
    {
        //can_manage_roles
        $superAdminType = config('app_permissions.fixedUserType')[1];//super_admin,
        $adminType = config('app_permissions.fixedUserType')[2];//admin,
        //target business - is_prime == true, then consider it as a super_admin/mother/parent company - software ownership
        //if logged in user is a super_admin, then show all users [with businesses]
        //if logged in user business is is_prime == true, then show only users of that business
        $contextManager = app(UserContextManager::class);
        $loggedInBusinessId = $contextManager->getBusinessId();
        $profile = $contextManager->getCurrentProfile();
        $loggedInUserType = $loggedInBusinessId ? $contextManager->getUserType() : null;
        $loggedInUserTypeId = $loggedInBusinessId ? $contextManager->getUserTypeId() : null;
        $allUserTypes = UserType::query()->where('status', 1)->get();
        $visibileUserTypeIds = $allUserTypes->where('visiblity', true)->pluck('id')->toArray();
        $inVisibileUserTypeIds = $allUserTypes->where('visiblity', 0)->pluck('id')->toArray();//special user types
        $loggedInUserProfile = $contextManager->getCurrentProfile();
        $loggedInUserTypeVisibility = $contextManager->getCurrentProfile()->userType->visiblity ?? false;
        $loggedInUserProfileId = $profile->id;

        $superAdminUserType = UserType::where('name', $superAdminType)->where('status', 1)->first();
        $superAdminTypeId = $superAdminUserType? $superAdminUserType->id : null;

        $dminUserType = UserType::where('name', $adminType)->where('status', 1)->first();
        $adminTypeId = $dminUserType? $dminUserType->id : null;

        $loggedInUserIsSoftwareOwnerCompany = false;
        if ($profile->business) {
            $loggedInUserIsSoftwareOwnerCompany = $profile->business->is_prime == true ? true : false;
        }

        $allowsUserIds = [];
        $allowsBusinessIds = [];
        $getUserProfiles = UserProfile::query()->select('user_id','business_id','user_type_id')->where('status',true);
        //if logged in user is a super_admin or admin, then show all users
        //($loggedInUserType == $superAdminType || $loggedInUserType == $adminType)
        if($loggedInUserIsSoftwareOwnerCompany && $loggedInUserType == $superAdminType){
            $allowsUserIds = $getUserProfiles->where('user_id', '!=', auth()->user()->id)->pluck('user_id')->toArray();
        }
        else if($loggedInUserIsSoftwareOwnerCompany && $loggedInUserType == $adminType){
            $allowsUserIds = $getUserProfiles->where('user_type_id', '!=', $superAdminTypeId)->pluck('user_id')->toArray();
        }
        else if(!$loggedInUserIsSoftwareOwnerCompany){
            $allowsUserIds = $getUserProfiles->where('business_id', $loggedInBusinessId)->where('user_type_id', '!=', $adminTypeId)->pluck('user_id')->toArray();
        }
        $allowsUserIds = array_values(array_unique($allowsUserIds));
        

        $query = User::query()->with([
            'profiles.business',
            'profiles.userType',
            'profiles.roles'
        ])->whereIn('id', $allowsUserIds)->where('status', true);

        if ($request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%");
        }
        $users = $query->orderBy('id', 'desc')->paginate(10);
        
        return view('cdbc.users.index', compact('users'));
    }
    */
    public function create()
    {
        $userTypes = UserType::where('status', 1)->get();
        $businesses = Business::where('status', 1)->get();
        return view('cdbc.users.create', compact('userTypes', 'businesses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'password' => 'required|min:6',
            'profiles.*.user_type_id' => 'required',
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

        if ($request->profiles) {
            foreach ($request->profiles as $p) {
                $user->profiles()->create([
                    'user_type_id' => $p['user_type_id'],
                    'business_id' => $p['business_id'] ?? null,
                    'default_login' => isset($p['default_login']) && $p['default_login'] ? 1 : 0,
                    'status' => 1
                ]);
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    public function edit(User $user)
    {
        $userTypes = UserType::where('status', 1)->get();
        $businesses = Business::where('status', 1)->get();
        return view('cdbc.users.create', compact('user', 'userTypes', 'businesses'));
    }

    public function update(Request $request, User $user)
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

        $user->profiles()->delete();
        if ($request->profiles) {
            foreach ($request->profiles as $p) {
                $user->profiles()->create([
                    'user_type_id' => $p['user_type_id'],
                    'business_id' => $p['business_id'] ?? null,
                    'default_login' => isset($p['default_login']) && $p['default_login'] ? 1 : 0,
                    'status' => 1
                ]);
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

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
