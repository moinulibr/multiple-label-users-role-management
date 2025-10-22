<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Services\FileUploader;

class UserController extends Controller
{

    protected FileUploader $uploader;

    public function __construct(FileUploader $uploader)
    {
        // Dependency Injection এর মাধ্যমে FileUploader-কে ইনজেক্ট করা
        $this->uploader = $uploader;
    }

    /**
     * Display a listing of the users (List).
     */
    public function index()
    {
        // রোল এবং পারমিশন সহ ইউজারদের তালিকা
        $users = User::with('roles')->latest()->paginate(10);
        //return UserResource::collection($users);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user (Create).
     */
    public function create()
    {
        $roles = Role::all();
        // Create এর জন্য একটি নতুন User ইনস্ট্যান্স পাস করা হলো
        $user = new User();
        $userRoleIds = [];
        return view('admin.users.create', compact('user', 'roles', 'userRoleIds'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
            //'profile_picture' => 'nullable|image|max:2048',
        ]);

        $fileData = $request->get('profile_image_base64')
            ?? $request->file('profile_image');
        $filePath = null;
        // 1. ফাইল আপলোড লজিক
        if ($fileData) {
            $filePath = $this->uploader->upload($request->file('profile_picture'), 'users');
        }

        // নতুন ইউজার তৈরি
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'] ?? null,
            // মডেল-এ cast: 'password' => 'hashed', থাকায় এখানে Hash::make() প্রয়োজন নেই, তবে রাখলে ক্ষতি নেই।
            //'profile_picture' => $filePath,
            'password' => Hash::make($validated['password']),
        ]);

        // রোল অ্যাসাইন করা
        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('admin.users.index')->with('success', 'User created successfully and roles assigned.');
    }

    /**
     * Display the specified user (View).
     */
    public function show(User $user)
    {
        //return new UserResource($user);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user (Edit).
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        // User এর বর্তমান রোল ID গুলি একটি অ্যারেতে নেওয়া
        $userRoleIds = $user->roles->pluck('id')->toArray();

        // create.blade.php কে edit পেজ হিসেবে ব্যবহার করার জন্য create ভিউ-এ পাস করা
        return view('admin.users.create', compact('user', 'roles', 'userRoleIds'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // বর্তমান ইউজারকে ignore করে unique চেক করা
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'mobile' => ['nullable', 'string', 'max:20'],
            // যদি পাসওয়ার্ড ফিল্ডে ইনপুট দেওয়া হয়, তবেই ভ্যালিডেট করা হবে
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],

            'profile_picture' => 'nullable|image|max:2048',
            //'remove_image' => 'nullable|boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'] ?? null,
        ];

        // যদি পাসওয়ার্ড ফিল্ডে কিছু লেখা থাকে, তবেই পাসওয়ার্ড আপডেট করা হবে
        if ($request->filled('password')) {
            $data['password'] = $validated['password'];
        }

        // 1. নতুন ফাইল আপলোড ও পুরাতন ফাইল ডিলিট লজিক
        if ($request->hasFile('profile_picture')) {
            // পুরাতন ইমেজ ডিলিট করা
            $this->uploader->delete($user->profile_picture);

            // নতুন ইমেজ আপলোড করা
            $data['profile_picture'] = $this->uploader->upload($request->file('profile_picture'), 'users');
        } elseif ($request->boolean('remove_image')) {
            // যদি remove_image চেকবক্স চেক করা থাকে এবং নতুন কোনো ইমেজ আপলোড না হয়
            $this->uploader->delete($user->profile_picture);
            $data['profile_picture'] = null;
        }

        $user->update($data);

        // রোল অ্যাসাইন করা (assignRoles মেথডের লজিক এখানে সরাসরি ব্যবহার করা হলো)
        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage (Delete).
     */
    public function destroy(User $user)
    {
        //if ($user->profile_image_path) {
           // $this->uploader->delete($user->profile_image_path);
        //}

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }


    /**
     * Show the form for assigning roles to a specific user. (NEW METHOD)
     */
    public function assignRoleForm(User $user)
    {
        $roles = Role::all();
        $userRoleIds = $user->roles->pluck('id')->toArray();
        return view('admin.users.assign-roles', compact('user', 'roles', 'userRoleIds'));
    }

    /**
     * Handle the assignment of roles to a specific user (Existing method).
     */
    public function assignRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        // sync() মেথডটি বিদ্যমান রোলগুলিকে নতুনগুলির সাথে প্রতিস্থাপন করে
        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('admin.users.index')->with('success', "Roles successfully assigned/updated for user: {$user->name}.");
    }
   
}



//use this in blade file
/* @if ($user->profile_image_path)
    {{-- সরাসরি অ্যাট্রিবিউট ব্যবহার করুন --}}
    <img src="{{ $user->profile_image_path }}" alt="Profile Image" class="rounded-circle" width="100">
@else
    {{-- ডিফল্ট ইমেজ --}}
    <img src="{{ asset('images/default-profile.png') }}" alt="Default Image" class="rounded-circle" width="100">
@endif 


Symlink তৈরি: প্রজেক্ট রুট ফোল্ডারে এই কমান্ডটি অবশ্যই একবার চালাতে হবে:
Bash
php artisan storage:link
কলামের নাম: আপনার ডেটাবেসে যেন profile_image_path নামে একটি কলাম থাকে, তা নিশ্চিত করুন।
*/