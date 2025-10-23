<?php

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'super_admin',
                'display_name' => 'System Administrator',
                'dashboard_key' => 'admin',
                'status' => true,
            ],
            [
                'name' => 'rent_owner',
                'display_name' => 'Rent Owner',
                'dashboard_key' => 'admin',
                'status' => true,
            ],
            [
                'name' => 'car_owner',
                'display_name' => 'Car Owner',
                'dashboard_key' => 'owner',
                'status' => true,
            ],
            [
                'name' => 'driver',
                'display_name' => 'Driver',
                'dashboard_key' => 'owner',
                'status' => true,
            ],
            [
                'name' => 'staff',
                'display_name' => 'Staff - Employee',
                'dashboard_key' => 'owner',
                'status' => true,
            ],
            [
                'name' => 'referral',
                'display_name' => 'General User - Referral',
                'dashboard_key' => 'general',
                'status' => true,
            ],
            [
                'name' => 'customer',
                'display_name' => 'General Customer',
                'dashboard_key' => 'customer',
                'status' => true,
            ],
        ];

        foreach ($types as $type) {
            UserType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
