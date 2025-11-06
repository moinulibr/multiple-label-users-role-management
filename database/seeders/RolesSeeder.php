<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // Config থেকে permissions নিয়ে আসা
        $permissionsConfig = config('app_permissions.modules', []);

        $allPermissions = [];

        foreach ($permissionsConfig as $module => $moduleData) {
            foreach ($moduleData['actions'] as $action => $actionData) {
                // permission string
                $allPermissions[] = $module . '.' . $action;
            }
        }

        // Super Admin Role
        Role::create([
            'name' => 'super_admin',
            'display_name' => 'Super Administrator',
            'permissions' => json_encode($allPermissions),
            'is_system' => true,
        ]);
    }
}
