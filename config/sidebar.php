<?php

return [
    [
        'title' => 'Dashboard',
        'icon' => 'mdi mdi-view-dashboard',
        'route' => 'dashboard',
        'permission' => 'dashboard.view',
        'isAllowedToAllContextLayer' => true,
    ],
    [
        'title' => 'Users',
        'icon' => 'mdi mdi-account-group',
        'permission' => 'users.manage',
        'isAllowedToAllContextLayer' => false,
        'contextLayer' => ['primary', 'secondary', 'sub-secondary'],
        'submenu' => [
            [
                'title' => 'User List',
                'route' => 'admin.users.index',
                'permission' => 'users.view',
                'isAllowedToAllContextLayer' => true,
            ],
            [
                'title' => 'Create User',
                'route' => 'admin.users.create',
                'permission' => 'users.create',
                'isAllowedToAllContextLayer' => false,
                'contextLayer' => ['primary', 'secondary'],
            ],
        ],
    ],
    [
        'title' => 'Businesses',
        'icon' => 'mdi mdi-grave-stone', //mdi-teach
        'permission' => 'businesses.manage',
        'isAllowedToAllContextLayer' => false,
        'contextLayer' => ['primary', 'secondary', 'sub-secondary'],
        'submenu' => [
            [
                'title' => 'Businesses List',
                'route' => 'admin.businesses.index',
                'permission' => 'businesses.view',
                'isAllowedToAllContextLayer' => true,
            ],
            [
                'title' => 'Create Business',
                'route' => 'admin.businesses.create',
                'permission' => 'businesses.create',
                'isAllowedToAllContextLayer' => false,
                'contextLayer' => ['primary', 'secondary'],
            ],
        ],
    ],
    [
        'title' => 'Roles',
        'icon' => 'mdi mdi-shield-account',
        'route' => 'admin.roles.index',
        'permission' => 'roles.manage',
        'isAllowedToAllContextLayer' => true,
    ],
    [
        'title' => 'Settings',
        'icon' => 'mdi mdi-settings',
        'route' => 'admin.settings.index',
        'permission' => 'settings.manage',
        'isAllowedToAllContextLayer' => true,
    ],
];
