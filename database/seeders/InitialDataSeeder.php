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
            // B. Primary Rent Owner (Owner of Rent Corp & Car Hire)
            'rent_owner_user' => [
                'name' => 'Primary Business Owner',
                'email' => 'primary.owner@gmail.com',
                'phone' => '8801700000002',
                'type' => 'rent_owner',
            ],
            // C. Secondary Owner/Partner (Owner of another Business)
            'sec_owner_user' => [
                'name' => 'Secondary Business Owner',
                'email' => 'secondary.owner@gmail.com',
                'phone' => '8801700000012',
                'type' => 'rent_owner',
            ],
            // D. Driver (Employee for multiple businesses)
            'driver_user' => [
                'name' => 'Driver User',
                'email' => 'driver@gmail.com',
                'phone' => '8801700000004',
                'type' => 'driver',
            ],
            // E. Staff (Employee for multiple profiles in one business)
            'staff_user' => [
                'name' => 'Staff Employee',
                'email' => 'staff@gmail.com',
                'phone' => '8801700000005',
                'type' => 'staff',
            ],
            // F. Customer (General User who can also be a staff in a business)
            'customer_user' => [
                'name' => 'General Customer or staff',
                'email' => 'customer@gmail.com',
                'phone' => '8801700000007',
                'type' => 'customer',
            ],
            // G. Car Owner (Tenant Partner) - Used for multi-profile later
            'car_owner_user' => [
                'name' => 'Car Owner User',
                'email' => 'car.owner@gmail.com',
                'phone' => '8801700000003',
                'type' => 'car_owner',
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
        // 3. Create Businesses (SECOND: now we have valid user_id)
        // 1. এক জন ইউজার একাধিক বিজনেসের মালিক হবে
        // =========================================================

        // A. Prime System Business (Admin)
        $primeBusiness = Business::firstOrCreate(['name' => 'Software Prime Ownership'], [
            'is_prime' => true,
            'hierarchy_level_id' => 1,
            'business_type' => 2, // Company
            'user_id' => $createdUsers['admin_user']->id,
            'default_login' => true,
            'slug' => 'prime-software',
            'email' => 'prime@software.com',
            'can_manage_roles' => true,
        ]);

        // B. Tenant Business 1 (Owner: Primary Rent Owner)
        $rentCorpBusiness = Business::firstOrCreate(['name' => 'Rent Management Corp'], [
            'is_prime' => false,
            'hierarchy_level_id' => 2,
            'business_type' => 2,
            'user_id' => $createdUsers['rent_owner_user']->id,
            'slug' => 'rent-corp',
            'default_login' => true,
            'email' => 'info@rentcorp.com',
            'can_manage_roles' => true,
        ]);

        // C. Tenant Business 2 (Owner: Primary Rent Owner, same user owns multiple businesses)
        $carHireBusiness = Business::firstOrCreate(['name' => 'Car Hire Agency'], [
            'is_prime' => false,
            'hierarchy_level_id' => 2,
            'business_type' => 1,
            'user_id' => $createdUsers['rent_owner_user']->id,
            'default_login' => false,
            'slug' => 'car-hire',
            'email' => 'info@carhire.com',
            'can_manage_roles' => true,
        ]);

        // D. Tenant Business 3 (Owner: Secondary Owner)
        $secBusiness = Business::firstOrCreate(['name' => 'Food Delivery Service'], [
            'is_prime' => false,
            'hierarchy_level_id' => 2,
            'business_type' => 2,
            'user_id' => $createdUsers['sec_owner_user']->id,
            'default_login' => false,
            'slug' => 'food-delivery',
            'email' => 'info@fooddelivery.com',
            'can_manage_roles' => true,
        ]);


        // =========================================================
        // 4. Create Roles (THIRD: Business IDs are available)
        // 2. roles table এ permissions insert করা
        // =========================================================

        // Permission array for Tenant Admin/Owner
        $tenantPermissions = [
            "users.manage",
            "users.assign",
            "users.create",
            "users.edit",
            "users.view",
            "roles.manage",
            "roles.assign",
            "settings.manage",
            "settings.view",
            "settings.update"
        ];

        // System-wide Roles (business_id = NULL)
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'business_id' => null], [
            'display_name' => 'Super Administrator',
            'permissions' => json_encode($tenantPermissions) // Example: Prime roles often have all permissions
        ]);
        $customerRole = Role::firstOrCreate(['name' => 'general_customer', 'business_id' => null], ['display_name' => 'Customer Access']);

        // Tenant Business 1 Roles (business_id = $rentCorpBusiness->id)
        $rentCorpOwnerRole = Role::firstOrCreate(['name' => 'tenant_owner', 'business_id' => $rentCorpBusiness->id], [
            'display_name' => 'Rent Owner Role',
            'permissions' => json_encode($tenantPermissions)
        ]);
        $rentCorpStaffRole = Role::firstOrCreate(['name' => 'staff_role', 'business_id' => $rentCorpBusiness->id], ['display_name' => 'Rent Staff Role']);

        // Tenant Business 2 Roles (business_id = $carHireBusiness->id)
        $carHireOwnerRole = Role::firstOrCreate(['name' => 'car_hire_owner', 'business_id' => $carHireBusiness->id], [
            'display_name' => 'Car Hire Owner Role',
            'permissions' => json_encode($tenantPermissions)
        ]);
        $carHireDriverRole = Role::firstOrCreate(['name' => 'driver_role', 'business_id' => $carHireBusiness->id], ['display_name' => 'Car Hire Driver Role']);

        // Tenant Business 3 Roles (business_id = $secBusiness->id)
        $foodOwnerRole = Role::firstOrCreate(['name' => 'food_owner', 'business_id' => $secBusiness->id], [
            'display_name' => 'Food Owner Role',
            'permissions' => json_encode($tenantPermissions)
        ]);
        $foodDeliveryStaffRole = Role::firstOrCreate(['name' => 'delivery_staff', 'business_id' => $secBusiness->id], ['display_name' => 'Delivery Staff Role']);


        // =========================================================
        // 5. Profile and Role Assignment (LAST)
        // 3. এক জন ইউজারকে একাধিক ব্যবসার আন্ডারে একাধিক প্রোফাইল দেওয়া
        // =========================================================

        // User Profiles to be created
        $userProfiles = [
            // 1. System Admin Profile
            [
                'userKey' => 'admin_user',
                'business' => $primeBusiness,
                'role' => $superAdminRole,
                'type' => 'super_admin',
                'default_login' => true,
            ],
            // 2. Rent Owner: Profile 1 (Owner of Rent Corp)
            [
                'userKey' => 'rent_owner_user',
                'business' => $rentCorpBusiness,
                'role' => $rentCorpOwnerRole,
                'type' => 'rent_owner',
                'default_login' => true, // Primary login profile
            ],
            // 3. Rent Owner: Profile 2 (Owner of Car Hire Agency)
            [
                'userKey' => 'rent_owner_user',
                'business' => $carHireBusiness,
                'role' => $carHireOwnerRole,
                'type' => 'rent_owner',
                'default_login' => false,
            ],
            // 4. Secondary Owner: Profile (Owner of Food Delivery)
            [
                'userKey' => 'sec_owner_user',
                'business' => $secBusiness,
                'role' => $foodOwnerRole,
                'type' => 'rent_owner',
                'default_login' => true,
            ],

            // --- Multi-Profile for Employees ---

            // 5. Driver: Profile 1 (Driver for Rent Management Corp)
            [
                'userKey' => 'driver_user',
                'business' => $rentCorpBusiness,
                'role' => $rentCorpStaffRole, // Giving driver a staff role for example
                'type' => 'driver',
                'default_login' => false,
            ],
            // 6. Driver: Profile 2 (Driver for Car Hire Agency)
            [
                'userKey' => 'driver_user',
                'business' => $carHireBusiness,
                'role' => $carHireDriverRole,
                'type' => 'driver',
                'default_login' => true, // Primary driver profile
            ],

            // 7. Staff: Profile 1 (Staff at Rent Corp)
            [
                'userKey' => 'staff_user',
                'business' => $rentCorpBusiness,
                'role' => $rentCorpStaffRole,
                'type' => 'staff',
                'default_login' => true,
            ],
            // 8. Staff: Profile 2 (Staff with Car Owner access at Rent Corp - same user, different type)
            [
                'userKey' => 'staff_user',
                'business' => $rentCorpBusiness,
                'role' => $rentCorpStaffRole,
                'type' => 'car_owner', // User has two profiles under the same business
                'default_login' => false,
            ],

            // 9. Customer: Profile 1 (General Customer)
            [
                'userKey' => 'customer_user',
                'business' => null,
                'role' => $customerRole,
                'type' => 'customer',
                'default_login' => true,
            ],
            // 10. Customer: Profile 2 (Delivery Staff at Food Delivery Service)
            [
                'userKey' => 'customer_user',
                'business' => $secBusiness,
                'role' => $foodDeliveryStaffRole,
                'type' => 'staff', // Customer user also works as staff
                'default_login' => false,
            ],
            // 11. Car Owner: Single profile for simplicity
            [
                'userKey' => 'car_owner_user',
                'business' => $rentCorpBusiness,
                'role' => $rentCorpStaffRole,
                'type' => 'car_owner',
                'default_login' => true,
            ],
        ];

        foreach ($userProfiles as $data) {
            $user = $createdUsers[$data['userKey']];
            $businessId = $data['business'] ? $data['business']->id : null;
            $roleId = $data['role']->id;
            $userTypeId = $userTypeIds->{$data['type']};

            // Create/Update Profile (user_profiles table)
            DB::table('user_profiles')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'user_type_id' => $userTypeId,
                    'business_id' => $businessId
                ],
                [
                    'default_login' => $data['default_login'],
                    'status' => 1
                ]
            );

            // Assign Role (user_roles table/pivot)
            // Note: We use syncWithoutDetaching to add roles for different business IDs
            $user->roles()->syncWithoutDetaching([
                $roleId => ['business_id' => $businessId]
            ]);
        }


        // =========================================================
        // 6. Login Platforms (Unchanged)
        // =========================================================

        $loginPlatforms = [
            // ... (Login Platforms data অপরিবর্তিত) ...
            [
                'name' => 'Web Admin Panel',
                'platform_key' => 'WEB_ADMIN_PANEL_KEY',
                'platform_hash_key' => '5eaaf16a98fae359e253d21e6bccb2c2',
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
                'platform_hash_key' => '5eaaf16a98fae359e253d21e6bccb2c2',
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
                'platform_hash_key' => '7be04cc5d13f672a4568074ebbb8fa92',
                'login_template_hash_key' => ['91ec1f9324753048c0096d036a694f86'],
                'status' => true,
            ],
            [
                'name' => 'Web Referral Panel',
                'platform_key' => 'WEB_REFERRAL_PANEL_KEY',
                'platform_hash_key' => '4568bd6bc631892dcda255e07ee9b3fa',
                'login_template_hash_key' => ['cd9bcdcbf9ef392bb2bce89a7c150638'],
                'status' => true,
            ]
        ];

        foreach ($loginPlatforms as $platform) {
            UserLoginPlatform::firstOrCreate(['name' => $platform['name']], $platform);
        }
    }
}
