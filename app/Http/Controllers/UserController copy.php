<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OldUserController extends Controller
{

    public function index()
    {
        // Eager load roles
        $users = User::with('roles')->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for editing the specified resource (Role Assign Form).
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        // User এর বর্তমান রোল ID গুলি একটি অ্যারেতে নেওয়া
        $userRoleIds = $user->roles->pluck('id')->toArray();
        return view('admin.users.edit', compact('user', 'roles', 'userRoleIds'));
    }

    /**
     * Handle the assignment of roles to a specific user.
     */
    public function assignRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        // sync() মেথডটি বিদ্যমান রোলগুলিকে নতুনগুলির সাথে প্রতিস্থাপন করে
        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('admin.users.index')->with('success', "Roles updated for user {$user->name}.");
    }

    // Resource Controller-এর অন্যান্য মেথডগুলি (create, store, update, destroy, show)
    // এই মডিউলের জন্য অপ্রয়োজনীয়, কারণ আমরা শুধু Role Assign করছি।
    // User তৈরি ও আপডেটের জন্য আমরা অন্য কোনো Auth Controller ব্যবহার করতে পারি।
    // তবে Error এড়ানোর জন্য এদেরকে খালি রাখা হলো:

    public function create()
    { /* For user creation form if needed */
    }
    public function store(Request $request)
    { /* For storing new user if needed */
    }
    public function update(Request $request, User $user)
    { /* For updating user details */
    }
    public function destroy(User $user)
    { /* For deleting user */
    }
    public function show(User $user)
    { /* For showing user details */
    }
    

}