<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Role;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ইউজার টাইপ লোড করা
        $userTypes = UserType::pluck('id', 'name')->toArray();
        $userTypeIds = (object)$userTypes;

        // =========================================================
        // 2. সকল ইউজার তৈরি করা (FIRST: to satisfy Foreign Key constraints)
        // =========================================================

        $usersToSeed = [
            // A. System Admin (Prime Business Owner)
            'admin_user' => [
                'name' => 'System Admin',
                'email' => 'admin@system.com',
                'phone' => '8801700000001',
                'type' => 'super_admin',
            ],
            // B. Rent Owner (Tenant Business Owner)
            'rent_owner_user' => [
                'name' => 'Rent Business Owner',
                'email' => 'owner@rent.com',
                'phone' => '8801700000002',
                'type' => 'rent_owner',
            ],
            // C. Car Owner (Tenant Partner)
            'car_owner_user' => [
                'name' => 'Car Owner User',
                'email' => 'car.owner@rent.com',
                'phone' => '8801700000003',
                'type' => 'car_owner',
            ],
            // D. Driver (Tenant Employee)
            'driver_user' => [
                'name' => 'Driver User',
                'email' => 'driver@rent.com',
                'phone' => '8801700000004',
                'type' => 'driver',
            ],
            // E. Staff (Tenant Employee)
            'staff_user' => [
                'name' => 'Staff Employee',
                'email' => 'staff@rent.com',
                'phone' => '8801700000005',
                'type' => 'staff',
            ],
            // F. Referral (General User)
            'referral_user' => [
                'name' => 'General Referral',
                'email' => 'referral@general.com',
                'phone' => '8801700000006',
                'type' => 'referral',
            ],
            // G. Customer (General User)
            'customer_user' => [
                'name' => 'General Customer',
                'email' => 'customer@general.com',
                'phone' => '8801700000007',
                'type' => 'customer',
            ],
        ];

        $createdUsers = [];
        foreach ($usersToSeed as $key => $userData) {
            $createdUsers[$key] = User::firstOrCreate(['email' => $userData['email']], [
                'name' => $userData['name'],
                'password' => Hash::make('password'),
                'phone' => $userData['phone'],
                'status' => 1,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]);
        }

        // =========================================================
        // 3. বিজনেস তৈরি করা (SECOND: now we have valid owner_user_id)
        // =========================================================

        // A. Prime System Business
        $primeBusiness = Business::firstOrCreate(['name' => 'Software Prime Ownership'], [
            'is_prime' => true,
            'business_type' => 2, // Company
            // 💡 এখন সঠিক ইউজার আইডি ব্যবহার করা হলো
            'owner_user_id' => $createdUsers['admin_user']->id,
            'slug' => 'prime-software',
            'email' => 'prime@software.com',
            'can_manage_roles' => true,
        ]);

        // B. Secondary Tenant Business
        $tenantBusiness = Business::firstOrCreate(['name' => 'Rent Management Corp'], [
            'is_prime' => false,
            'business_type' => 2,
            // 💡 এখন সঠিক ইউজার আইডি ব্যবহার করা হলো
            'owner_user_id' => $createdUsers['rent_owner_user']->id,
            'slug' => 'rent-corp',
            'email' => 'info@rentcorp.com',
            'can_manage_roles' => true,
        ]);

        // =========================================================
        // 4. রোল তৈরি করা (THIRD: Business IDs are available)
        // =========================================================

        // System-wide Roles (business_id = NULL)
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'business_id' => null], ['display_name' => 'Super Administrator', 'permissions' => ['system.all']]);
        $customerRole = Role::firstOrCreate(['name' => 'general_customer', 'business_id' => null], ['display_name' => 'Customer Access', 'permissions' => ['customer.view_history']]);
        $referralRole = Role::firstOrCreate(['name' => 'general_referral', 'business_id' => null], ['display_name' => 'Referral Program Access', 'permissions' => ['referral.dashboard']]);

        // Tenant Business Roles (business_id = $tenantBusiness->id)
        $tenantOwnerRole = Role::firstOrCreate(['name' => 'tenant_owner', 'business_id' => $tenantBusiness->id], ['display_name' => 'Business Owner Role (Tenant)', 'permissions' => ['tenant.manage_all']]);
        $carOwnerRole = Role::firstOrCreate(['name' => 'car_owner_role', 'business_id' => $tenantBusiness->id], ['display_name' => 'Car Owner Role (Tenant)', 'permissions' => ['car.view_own']]);
        $driverRole = Role::firstOrCreate(['name' => 'driver_role', 'business_id' => $tenantBusiness->id], ['display_name' => 'Driver Role (Tenant)', 'permissions' => ['trip.view_assigned']]);
        $staffRole = Role::firstOrCreate(['name' => 'staff_role', 'business_id' => $tenantBusiness->id], ['display_name' => 'Staff/Employee Role (Tenant)', 'permissions' => ['booking.manage']]);

        // =========================================================
        // 5. প্রোফাইল ও রোল অ্যাসাইনমেন্ট (LAST)
        // =========================================================

        // এই ধাপটি User, Business এবং Role ID-এর উপর নির্ভর করে।
        $assignments = [
            'admin_user' => ['role' => $superAdminRole, 'business' => $primeBusiness->id, 'is_primary' => true, 'type' => 'super_admin'],
            'rent_owner_user' => ['role' => $tenantOwnerRole, 'business' => $tenantBusiness->id, 'is_primary' => true, 'type' => 'rent_owner'],
            'car_owner_user' => ['role' => $carOwnerRole, 'business' => $tenantBusiness->id, 'is_primary' => false, 'type' => 'car_owner'],
            'driver_user' => ['role' => $driverRole, 'business' => $tenantBusiness->id, 'is_primary' => false, 'type' => 'driver'],
            'staff_user' => ['role' => $staffRole, 'business' => $tenantBusiness->id, 'is_primary' => false, 'type' => 'staff'],
            'referral_user' => ['role' => $referralRole, 'business' => null, 'is_primary' => true, 'type' => 'referral'],
            'customer_user' => ['role' => $customerRole, 'business' => null, 'is_primary' => true, 'type' => 'customer'],
        ];

        foreach ($assignments as $userKey => $data) {
            $user = $createdUsers[$userKey];

            // প্রোফাইল তৈরি/আপডেট করা
            DB::table('user_profiles')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'user_type_id' => $userTypeIds->{$data['type']},
                    'business_id' => $data['business']
                ],
                [
                    'is_primary' => $data['is_primary'],
                    'status' => 1
                ]
            );

            // রোল অ্যাসাইন করা
            $user->roles()->syncWithoutDetaching([
                $data['role']->id => ['business_id' => $data['business']]
            ]);
        }
    }
}
