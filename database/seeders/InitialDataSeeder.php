<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Role;
use App\Models\User;
use App\Models\UserLoginPlatform;
use App\Models\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Load User Types
        $userTypes = UserType::pluck('id', 'name')->toArray();
        $userTypeIds = (object)$userTypes;

        // =========================================================
        // 2. Create All Users (FIRST: to satisfy Foreign Key constraints)
        // =========================================================

        $usersToSeed = [
            // A. System Admin (Prime Business Owner)
            'admin_user' => [
                'name' => 'System Admin',
                'email' => 'admin@gmail.com',
                'phone' => '8801700000001',
                'type' => 'super_admin',
            ],
            // B. Rent Owner (Tenant Business Owner)
            'rent_owner_user' => [
                'name' => 'Rent Business Owner',
                'email' => 'business.owner@gmail.com',
                'phone' => '8801700000002',
                'type' => 'rent_owner',
            ],
            // C. Car Owner (Tenant Partner)
            'car_owner_user' => [
                'name' => 'Car Owner User',
                'email' => 'car.owner@gmail.com',
                'phone' => '8801700000003',
                'type' => 'car_owner',
            ],
            // D. Driver (Tenant Employee)
            'driver_user' => [
                'name' => 'Driver User',
                'email' => 'driver@gmail.com',
                'phone' => '8801700000004',
                'type' => 'driver',
            ],
            // E. Staff (Tenant Employee)
            'staff_user' => [
                'name' => 'Staff Employee',
                'email' => 'staff@gmail.com',
                'phone' => '8801700000005',
                'type' => 'staff',
            ],
            // F. Referral (General User)
            'referral_user' => [
                'name' => 'General Referral',
                'email' => 'referral@gmail.com',
                'phone' => '8801700000006',
                'type' => 'referral',
            ],
            // G. Customer (General User)
            'customer_user' => [
                'name' => 'General Customer',
                'email' => 'customer@gmail.com',
                'phone' => '8801700000007',
                'type' => 'customer',
            ],
        ];

        $createdUsers = [];
        foreach ($usersToSeed as $key => $userData) {
            $createdUsers[$key] = User::firstOrCreate(['email' => $userData['email']], [
                'name' => $userData['name'],
                'password' => Hash::make('123456'),
                'phone' => $userData['phone'],
                'status' => 1,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]);
        }

        // =========================================================
        // 3. Create Businesses (SECOND: now we have valid owner_user_id)
        // =========================================================

        // A. Prime System Business
        $primeBusiness = Business::firstOrCreate(['name' => 'Software Prime Ownership'], [
            'is_prime' => true,
            'business_type' => 2, // Company
            //Using the correct User ID now
            'owner_user_id' => $createdUsers['admin_user']->id,
            'slug' => 'prime-software',
            'email' => 'prime@software.com',
            'can_manage_roles' => true,
        ]);

        // B. Secondary Tenant Business
        $tenantBusiness = Business::firstOrCreate(['name' => 'Rent Management Corp'], [
            'is_prime' => false,
            'business_type' => 2,
            //Using the correct User ID now
            'owner_user_id' => $createdUsers['rent_owner_user']->id,
            'slug' => 'rent-corp',
            'email' => 'info@rentcorp.com',
            'can_manage_roles' => true,
        ]);

        // =========================================================
        // 4. Create Roles (THIRD: Business IDs are available)
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
        // 5. Profile and Role Assignment (LAST)
        // =========================================================

        // This step depends on User, Business, and Role IDs being available.
        $assignments = [
            'admin_user' => ['role' => $superAdminRole, 'business' => $primeBusiness->id, 'default_login' => true, 'type' => 'super_admin'],
            'rent_owner_user' => ['role' => $tenantOwnerRole, 'business' => $tenantBusiness->id, 'default_login' => true, 'type' => 'rent_owner'],
            'car_owner_user' => ['role' => $carOwnerRole, 'business' => $tenantBusiness->id, 'default_login' => false, 'type' => 'car_owner'],
            'driver_user' => ['role' => $driverRole, 'business' => $tenantBusiness->id, 'default_login' => false, 'type' => 'driver'],
            'staff_user' => ['role' => $staffRole, 'business' => $tenantBusiness->id, 'default_login' => false, 'type' => 'staff'],
            'referral_user' => ['role' => $referralRole, 'business' => null, 'default_login' => true, 'type' => 'referral'],
            'customer_user' => ['role' => $customerRole, 'business' => null, 'default_login' => true, 'type' => 'customer'],
        ];

        foreach ($assignments as $userKey => $data) {
            $user = $createdUsers[$userKey];

            // Create/Update Profile
            DB::table('user_profiles')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'user_type_id' => $userTypeIds->{$data['type']},
                    'business_id' => $data['business']
                ],
                [
                    'default_login' => $data['default_login'],
                    'status' => 1
                ]
            );

            // Assign Role
            $user->roles()->syncWithoutDetaching([
                $data['role']->id => ['business_id' => $data['business']]
            ]);
        }

        //login platforms
        $loginPlatforms = [
            [
                'name' => 'Web Admin Panel',
                'platform_key' => 'WEB_ADMIN_PANEL_KEY',
                'platform_hash_key' => '5eaaf16a98fae359e253d21e6bccb2c2',  // Frontend will send this string
                // This array contains the hash keys of roles (from user_types) allowed to log in via this platform.
                'login_template_hash_key' => [
                    'ed49c3fed75a513a79cb8bd1d4715d57',
                    'e8b57d0da4580dc2ca4e10512f0cab39',
                    '2e768fdf59df1b11930dcbb9a257f62f',
                    'e2d45d57c7e2941b65c6ccd64af4223e',
                    '1253208465b1efa876f982d8a9e73eef',
                    '91ec1f9324753048c0096d036a694f86',
                    'cd9bcdcbf9ef392bb2bce89a7c150638'
                ],
                'status' => true,
            ],
            [
                'name' => 'Web Admin Login Panel',
                'platform_key' => 'WEB_ADMIN_PANEL_KEY',
                'platform_hash_key' => '5eaaf16a98fae359e253d21e6bccb2c2',  // Frontend will send this string
                // This array contains the hash keys of roles (from user_types) allowed to log in via this platform.
                'login_template_hash_key' => [
                    'ed49c3fed75a513a79cb8bd1d4715d57',
                    'e8b57d0da4580dc2ca4e10512f0cab39',
                    '2e768fdf59df1b11930dcbb9a257f62f',
                    'e2d45d57c7e2941b65c6ccd64af4223e',
                    '1253208465b1efa876f982d8a9e73eef',
                    '91ec1f9324753048c0096d036a694f86',
                    'cd9bcdcbf9ef392bb2bce89a7c150638'
                ],
                'status' => false,
            ],
            [
                'name' => 'Web Customer Panel',
                'platform_key' => 'WEB_CUSTOMER_PANEL_KEY',
                'platform_hash_key' => '7be04cc5d13f672a4568074ebbb8fa92', // Frontend will send this string
                // This array contains the hash keys of roles (from user_types) allowed to log in via this platform.
                'login_template_hash_key' => ['91ec1f9324753048c0096d036a694f86'],
                'status' => true,
            ],
            [
                'name' => 'Web Referral Panel',
                'platform_key' => 'WEB_REFERRAL_PANEL_KEY',
                'platform_hash_key' => '4568bd6bc631892dcda255e07ee9b3fa', // Frontend will send this string
                // This array contains the hash keys of roles (from user_types) allowed to log in via this platform.
                'login_template_hash_key' => ['cd9bcdcbf9ef392bb2bce89a7c150638'],
                'status' => true,
            ]
        ];

        foreach ($loginPlatforms as $platform) {
            UserLoginPlatform::firstOrCreate(['name' => $platform['name']], $platform);
        }

    }
}
