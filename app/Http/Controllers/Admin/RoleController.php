<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Services\UserContextManager;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function index()
    {
        $contextManager = app(UserContextManager::class);
        $business_id = $businessId ?? $contextManager->getBusinessId();

        $roles = Role::latest()->paginate(10);
        return view('cdbc.roles.index', compact('roles'));
    }

    public function create()
    {
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
        $currentContext = $contextManager->getUserContextLayer(); // e.g. 'primary'
        $config = config('app_permissions.modules', []);
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
}
