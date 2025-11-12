<?php


namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
class RoleController extends Controller
{
    // show list
    public function index()
    {
        $roles = Role::with('business')->latest()->paginate(20);
        return view('cdbc.roles.index', compact('roles'));
    }


    // create form
    public function create()
    {

        // build permissions grouped by module filtered by current user context
        $permissionConfig = config('app_permissions.modules', []);
        $contextManager = app(\App\Services\UserContextManager::class);
        $userContext = $contextManager->getUserContextLayer();


        // filter modules/actions by config context rules
        $grouped = [];
        foreach ($permissionConfig as $module => $data) {
            $actions = [];
            foreach ($data['actions'] as $action => $rules) {
                // show to form only if allowed for this context OR allowed to all
                $allowedContexts = $rules['contextLayer'] ?? [];
                if (!empty($rules['isAllowedToAllContextLayer']) || in_array($userContext, $allowedContexts)) {
                    $actions[] = "{$module}.{$action}";
                }
            }
            if (!empty($actions)) {
                $grouped[$module] = $actions;
            }
        }
        $isEdit = false;
        return view('cdbc.roles.create', compact('grouped', 'isEdit'));
    }


    // store
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'display_name' => 'nullable|string|max:191',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'business_id' => 'nullable|integer|exists:businesses,id',
        ]);


        $role = Role::create([
            'name' => $data['name'],
            'display_name' => $data['display_name'] ?? null,
            'description' => $data['description'] ?? null,
            'business_id' => $data['business_id'] ?? null,
            'permissions' => json_encode($data['permissions'] ?? []),
        ]);
    }
    public function edit(Role $role)
    {
        $permissionConfig = config('app_permissions.modules', []);
        $contextManager = app(\App\Services\UserContextManager::class);
        $userContext = $contextManager->getUserContextLayer();


        $grouped = [];
        foreach ($permissionConfig as $module => $data) {
            $actions = [];
            foreach ($data['actions'] as $action => $rules) {
                $allowedContexts = $rules['contextLayer'] ?? [];
                if (!empty($rules['isAllowedToAllContextLayer']) || in_array($userContext, $allowedContexts)) {
                    $actions[] = "{$module}.{$action}";
                }
            }
            if (!empty($actions)) {
                $grouped[$module] = $actions;
            }
        }


        $rolePermissions = json_decode($role->permissions, true) ?? [];
        return view('cdbc.roles.edit', compact('role', 'grouped', 'rolePermissions'));
    }


    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'display_name' => 'nullable|string|max:191',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);


        $role->update([
            'display_name' => $data['display_name'] ?? $role->display_name,
            'description' => $data['description'] ?? $role->description,
            'permissions' => json_encode($data['permissions'] ?? []),
        ]);

        // clear permission cache for users who have this role (optional: implement job)
        // example: dispatch(new \App\Jobs\ClearRoleRelatedCaches($role));

        return Redirect::route('cdbc.roles.index')->with('success', 'Role updated');
    }


    public function destroy(Role $role)
    {
        $role->delete();
        return Redirect::route('cdbc.roles.index')->with('success', 'Role deleted');
    }
}
