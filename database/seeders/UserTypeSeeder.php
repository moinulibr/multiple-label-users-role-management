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
                'login_panel_key' => 'super_admin',
                'login_panel_hash_key' => 'ed49c3fed75a513a79cb8bd1d4715d57',
                'status' => true,
            ],
            [
                'name' => 'rent_owner',
                'display_name' => 'Rent Owner',
                'dashboard_key' => 'admin',
                'login_panel_key' => 'rent_owner',
                'login_panel_hash_key' => 'e8b57d0da4580dc2ca4e10512f0cab39',
                'status' => true,
            ],
            [
                'name' => 'car_owner',
                'display_name' => 'Car Owner',
                'dashboard_key' => 'owner',
                'login_panel_key' => 'car_owner',
                'login_panel_hash_key' => '2e768fdf59df1b11930dcbb9a257f62f',
                'status' => true,
            ],
            [
                'name' => 'driver',
                'display_name' => 'Driver',
                'dashboard_key' => 'owner',
                'login_panel_key' => 'driver',
                'login_panel_hash_key' => 'e2d45d57c7e2941b65c6ccd64af4223e',
                'status' => true,
            ],
            [
                'name' => 'staff',
                'display_name' => 'Staff - Employee',
                'dashboard_key' => 'owner',
                'login_panel_key' => 'staff',
                'login_panel_hash_key' => '1253208465b1efa876f982d8a9e73eef',
                'status' => true,
            ],
            [
                'name' => 'referral',
                'display_name' => 'General User - Referral',
                'dashboard_key' => 'general',
                'login_panel_key' => 'referral',
                'login_panel_hash_key' => 'cd9bcdcbf9ef392bb2bce89a7c150638',
                'status' => true,
            ],
            [
                'name' => 'customer',
                'display_name' => 'General Customer',
                'dashboard_key' => 'customer',
                'login_panel_key' => 'customer',
                'login_panel_hash_key' => '91ec1f9324753048c0096d036a694f86',
                'status' => true,
            ],
        ];

        foreach ($types as $type) {
            UserType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
