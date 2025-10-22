<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class RoleControllerOld extends Controller
{
    /**
     * Display a listing of the resource (Role List).
     */
    public function index(Request $request)
    {
        /* $roles = Role::all();
        return view('admin.roles.index', compact('roles')); */
        $roles = Role::all();

        // Index page content
        $view = view('admin.roles.index', compact('roles'));

        // **NEW AJAX CHECK for INDEX**
        if ($request->ajax()) {
            return $view->render(); // Return only the HTML content
        }

        return $view;
    }

    /**
     * Show the form for creating a new resource (Role Create Form).
     */
    public function create(Request $request)
    {
        $permissions = Config::get('app_permissions.modules');
        $role = new Role();
        if ($request->ajax()) {
            // If AJAX, return only the form content for the modal
            return view('admin.roles.form_content', compact('permissions', 'role'));
        }
        return view('admin.roles.form', compact('permissions', 'role'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
            'name' => 'nullable|string|unique:roles,name',
        ]);

        $name = $validated['name'] ?? Str::slug($validated['display_name'], '_');

        Role::create([
            'name' => Str::slug($name, '_'),
            'display_name' => $validated['display_name'],
            'permissions' => $request->input('permissions', []),
        ]);

        // Success: Redirect on web, or return 200/empty response on AJAX
        if ($request->ajax()) {
            return response()->json(['success' => true, 'redirect' => route('admin.roles.index')]);
        }
        return redirect()->route('admin.roles.index')->with('success', 'Role successfully created!');
        
        /* $validated = $request->validate([
            'display_name' => 'required|string|max:255',
            'name' => 'nullable|string|unique:roles,name', // name যদি না দেওয়া হয়, display_name থেকে তৈরি হবে
        ]);

        $name = $validated['name'] ?? Str::slug($validated['display_name'], '_');
        $permissions = $request->input('permissions', []);

        Role::create([
            'name' => $name,
            'display_name' => $validated['display_name'],
            'permissions' => $permissions,
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role successfully created and permissions assigned!');
         */
    }

    /**
     * Show the form for editing the specified resource (Role Edit Form).
     */
    public function edit(Role $role, Request $request)
    {
        $permissions = Config::get('app_permissions.modules');

        if ($request->ajax()) {
            // If AJAX, return only the form content for the modal
            return view('admin.roles.form_content', compact('permissions', 'role'));
        }

        // Fallback for direct GET request
        return view('admin.roles.form_page', compact('permissions', 'role'));

        /* $permissions = Config::get('app_permissions.modules');
        return view('admin.roles.form', compact('permissions', 'role')); */
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
            'name' => 'nullable|string|unique:roles,name,' . $role->id,
        ]);

        $name = $validated['name'] ?? $role->name;

        $role->update([
            'name' => Str::slug($name, '_'),
            'display_name' => $validated['display_name'],
            'permissions' => $request->input('permissions', []),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'redirect' => route('admin.roles.index')]);
        }
        return redirect()->route('admin.roles.index')->with('success', 'Role successfully updated!');
        /* $validated = $request->validate([
            'display_name' => 'required|string|max:255',
            'name' => 'nullable|string|unique:roles,name,' . $role->id,
        ]);
        
        $name = $validated['name'] ?? $role->name; 
        $permissions = $request->input('permissions', []);

        $role->update([
            'name' => Str::slug($name, '_'),
            'display_name' => $validated['display_name'],
            'permissions' => $permissions,
        ]);
        
        return redirect()->route('admin.roles.index')->with('success', 'Role successfully updated!'); */
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if ($role->name === 'super_admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Super Admin role cannot be deleted.');
        }
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
        /* if ($role->name === 'super_admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Super Admin role cannot be deleted.');
        }
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.'); */
    }


    public function show(Role $role)
    {
        return redirect()->route('admin.roles.edit', $role);
    }
}
