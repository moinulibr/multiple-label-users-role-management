<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected RoleService $service;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
        $this->middleware(['auth', 'permission:roles.manage']);
    }

    public function index()
    {
        $roles = Role::with('permissions')->latest()->paginate(20);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('group')->get();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'display_name' => 'required|string',
            'name' => 'nullable|string',
            'permission_ids' => 'nullable|array',
        ]);

        $businessId = session('current_business_id'); // create role in current business context
        $this->service->createRole($data, $businessId);

        return back()->with('success', 'Role created');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('group')->get();
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'display_name' => 'required|string',
            'permission_ids' => 'nullable|array',
        ]);

        $this->service->updateRole($role, $data);
        return back()->with('success', 'Role updated');
    }

    public function destroy(Role $role)
    {
        if ($role->is_special) {
            return back()->with('error', 'System role cannot be deleted');
        }
        $role->delete();
        return back()->with('success', 'Role deleted');
    }
}
