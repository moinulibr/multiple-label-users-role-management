<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\UserType;
use App\Models\UserProfile;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BusinessController extends Controller
{
    /**
     * Show the form for creating a new business.
     */
    public function create()
    {
        // সমস্ত অ্যাক্টিভ ইউজার এবং প্যারেন্ট বিজনেস লোড করা হচ্ছে
        $users = User::where('status', 1)->get(['id', 'name', 'phone']);
        $parentBusinesses = Business::all(['id', 'name']);

        return view('businesses.create', compact('users', 'parentBusinesses'));
    }

    /**
     * Store a newly created business and assign owner profile.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:businesses,name',
            'user_id' => 'required|exists:users,id',
            'email' => 'nullable|email|max:120|unique:businesses,email',
            'phone2' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:150',
            'parent_business_id' => 'nullable|exists:businesses,id',
            'is_prime' => 'boolean',
            'can_manage_roles' => 'boolean',
            // Default business owner user type key
            'default_owner_type_key' => 'required|string',
        ]);

        // Transaction ensures atomicity (Business and Profile creation)
        try {
            DB::beginTransaction();

            // 1. Create the Business
            $business = Business::create([
                'name' => $validated['name'],
                'slug' => \Str::slug($validated['name']), // Assuming you are using Laravel Str helper
                'user_id' => $validated['user_id'],
                'email' => $validated['email'],
                'phone2' => $validated['phone2'],
                'address' => $validated['address'],
                'website' => $validated['website'],
                'parent_business_id' => $validated['parent_business_id'],
                'is_prime' => $request->has('is_prime'),
                'can_manage_roles' => $request->has('can_manage_roles'),
                'default_login' => true, // Owner's business is generally default login
                'hierarchy_level_id' => $validated['parent_business_id'] ? 2 : 1, // Simple hierarchy logic
            ]);

            // 2. Assign the default profile (e.g., 'rent_owner' or 'super_admin' based on logic)
            $ownerUserType = UserType::where('name', $validated['default_owner_type_key'])->firstOrFail();

            UserProfile::create([
                'user_id' => $validated['user_id'],
                'user_type_id' => $ownerUserType->id,
                'business_id' => $business->id,
                'default_login' => true,
                'status' => 1,
            ]);

            DB::commit();

            return redirect()->route('businesses.create')->with('success', 'নতুন বিজনেস ও মালিকের প্রোফাইল সফলভাবে তৈরি করা হয়েছে!');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error and return a user-friendly message
            return back()->withInput()->with('error', 'বিজনেস তৈরি করার সময় একটি ত্রুটি হয়েছে: ' . $e->getMessage());
        }
    }
}
