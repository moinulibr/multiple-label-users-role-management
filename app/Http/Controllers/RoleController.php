<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource (Role List).
     */
    public function index(Request $request)
    {
        // Role::all() ব্যবহার করা হয়েছে, তবে প্রোডাকশনে অবশ্যই Role::paginate() ব্যবহার করা উচিত।
        $roles = Role::all();

        $view = view('admin.roles.index', compact('roles'));

        // AJAX অনুরোধের জন্য শুধুমাত্র লিস্ট কম্পোনেন্টের HTML রিটার্ন করা হলো
        if ($request->ajax()) {
            return $view->render();
        }

        return $view;
    }

    /**
     * Show the form for creating a new resource (Role Create Form).
     */
    public function create(Request $request)
    {
        // 'app_permissions.modules' কনফিগ থেকে পারমিশন লোড করা হচ্ছে
        $permissions = Config::get('app_permissions.modules');
        $role = new Role();
        return view('admin.roles.create', compact('permissions', 'role'));

        $form_content_view = view('admin.roles.form_content', compact('permissions', 'role'));

        if ($request->ajax()) {
            // AJAX অনুরোধের জন্য শুধু ফর্ম কন্টেন্ট রিটার্ন করা হলো (যেমন - মডেলের জন্য)
            return $form_content_view;
        }

        // সাধারণ GET অনুরোধের জন্য ফুল পেজ ভিউ
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
            'permissions' => 'nullable|array',
        ]);

        $name = $validated['name'] ?? Str::slug($validated['display_name'], '_');

        Role::create([
            'name' => Str::slug($name, '_'),
            'display_name' => $validated['display_name'],
            'permissions' => $request->input('permissions', []),
        ]);

        // AJAX সফল হলে JSON রেসপন্স
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Role successfully created!', 'redirect' => route('admin.roles.index')]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role successfully created!');
    }

    /**
     * Show the form for editing the specified resource (Role Edit Form).
     */
    public function edit(Role $role, Request $request)
    {
        $permissions = Config::get('app_permissions.modules');


        // create.blade.php ফাইলটিই edit মোডে ব্যবহার করা হবে
        return view('admin.roles.create', compact('role', 'permissions'));

        $permissions = Config::get('app_permissions.modules');

        $form_content_view = view('admin.roles.form_content', compact('permissions', 'role'));

        if ($request->ajax()) {
            return $form_content_view;
        }

        return view('admin.roles.form', compact('permissions', 'role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
            // বর্তমান রোল আইডি বাদ দিয়ে নাম ইউনিক চেক করা
            'name' => ['nullable', 'string', Rule::unique('roles', 'name')->ignore($role->id)],
            'permissions' => 'nullable|array',
        ]);

        $name = $validated['name'] ?? $role->name;

        $role->update([
            'name' => Str::slug($name, '_'),
            'display_name' => $validated['display_name'],
            'permissions' => $request->input('permissions', []),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Role successfully updated!', 'redirect' => route('admin.roles.index')]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // 'super_admin' রোল ডিলিট করা রোধ করা
        if ($role->name === 'super_admin') {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'error' => 'Super Admin role cannot be deleted.'], 403);
            }
            return redirect()->route('admin.roles.index')->with('error', 'Super Admin role cannot be deleted.');
        }

        $role->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Role deleted successfully.']);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    /**
     * Redirects show() method to edit() method.
     */
    public function show(Role $role)
    {
        return redirect()->route('admin.roles.edit', $role);
    }
}
