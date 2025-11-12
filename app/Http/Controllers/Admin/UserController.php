<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserType;
use App\Models\Business;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%");
        }

        $users = $query->orderBy('id', 'desc')->paginate(10);
        return view('cdbc.users.index', compact('users'));
    }

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
