<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\UserType;
use App\Services\UserContextManager;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function index()
    {
        //return $this->getContextBasedPermissions();
        $contextManager = app(UserContextManager::class);
        $business_id = $businessId ?? $contextManager->getBusinessId();

        $profile = $contextManager->getCurrentProfile();
        $isPrimeCompany = $profile->business?->is_prime ?? false;

        $superAdminType = config('app_permissions.fixedUserType')[1];
        $adminType = config('app_permissions.fixedUserType')[2];


        $roles = Role::where('business_id', $business_id)->latest()->paginate(10);

        return view('cdbc.roles.index', compact('roles'));
    }

    public function create()
    {
        $contextManager = app(UserContextManager::class);
        $business_id = $businessId ?? $contextManager->getBusinessId();
        $profile = $contextManager->getCurrentProfile();
        $permissions = $this->getContextBasedPermissions();
        return view('cdbc.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'display_name' => 'nullable|string|max:120',
            'permissions' => 'nullable|array',
        ]);

        $contextManager = app(UserContextManager::class);
        $business_id = $contextManager->getBusinessId();
        Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'permissions' => json_encode($request->permissions ?? []),
            'business_id' => $business_id ?? null,
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $permissions = $this->getContextBasedPermissions();
        $selectedPermissions = json_decode($role->permissions ?? '[]', true);
        //$selectedPermissions = $this->getContextBasedPermissions();
        return view('cdbc.roles.edit', compact('role', 'permissions', 'selectedPermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,' . $role->id,
            'display_name' => 'nullable|string|max:120',
            'permissions' => 'nullable|array',
        ]);

        $role->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'permissions' => json_encode($request->permissions ?? []),
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    private function getContextBasedPermissions()
    {
        $contextManager = app(UserContextManager::class);
        $business_id = $businessId ?? $contextManager->getBusinessId();
        $profile = $contextManager->getCurrentProfile();
        $isPrimeCompany = $profile->business?->is_prime ?? false;
        $userType = $contextManager->getUserType();
        $userTypeId = $contextManager->getUserTypeId();
        $superAdminType = config('app_permissions.fixedUserType')[1];
        $adminType = config('app_permissions.fixedUserType')[2];

        /* $userTypes = UserType::where('status', 1)
            ->whereIn('name', [$superAdminType, $adminType])
            ->get()
            ->keyBy('name');

        $superAdminTypeId = $userTypes[$superAdminType]->id ?? null;
        $adminTypeId = $userTypes[$adminType]->id ?? null; */

        $isPrimeCompany = $profile->business?->is_prime ?? false;

        $currentContext = $contextManager->getUserContextLayer(); // e.g. 'primary'
        $config = config('app_permissions.modules', []);
        $permissions = [];

        if ($isPrimeCompany) {
            if ($userType == $superAdminType) {
                $permissions = $this->adminUserBasePermissionLists($config, $currentContext);
            } else if ($userType === $adminType) {
                $permissions = $this->adminUserBasePermissionLists($config, $currentContext);
            } else {
                $permissions = $this->singleUserBasePermissionLists();
            }
        } else {
             if ($userType == $adminType) {
                $permissions = $this->adminUserBasePermissionLists($config, $currentContext);
            } else {
                $permissions = $this->singleUserBasePermissionLists();
            }
        }

        return $permissions;
    }

    private function adminUserBasePermissionLists($config, $currentContext)
    {
        $permissions = [];
        foreach ($config as $module => $data) {
            foreach ($data['actions'] as $action => $rules) {
                $allowed = $rules['isAllowedToAllContextLayer']
                    || (isset($rules['contextLayer']) && in_array($currentContext, $rules['contextLayer']));

                if ($allowed) {
                    $permissions[$module][$action] = ucfirst($action);
                }
            }
        }

        return $permissions;
    }

    public function singleUserBasePermissionLists()
    {
        $contextManager = app(UserContextManager::class);
        $business_id = $businessId ?? $contextManager->getBusinessId();
        $profile = $contextManager->getCurrentProfile();
        $userProfileId = $contextManager->getUserProfileId();
        $assignedPermissions =   $profile->where('id', $userProfileId)
            ->with(['roles' => function ($query) use ($business_id, $userProfileId) {
                // Load only the specific role attached to this profile
                //$query->where('roles.id', $targetRoleId);
                $query->wherePivot('business_id' , $business_id)->wherePivot('user_profile_id' , $userProfileId);
                // $query->orderBy('roles.is_primary', 'desc')->take(1);
            }])
            ->first()
            ?->roles
            ->first()
            ?->permissions
            ?? [];

        if (is_string($assignedPermissions)) {
            $assignedPermissions = json_decode($assignedPermissions, true);
        }
        if (is_null($assignedPermissions)) {
            $assignedPermissions = [];
        }

        $structuredPermissions = [];

        foreach ($assignedPermissions as $permission) {

            list($module, $action) = explode('.', $permission, 2);

            if (!isset($structuredPermissions[$module])) {
                $structuredPermissions[$module] = [];
            }

            $structuredPermissions[$module][$action] = ucfirst($action);
        }

        return $structuredPermissions;
    }
}
