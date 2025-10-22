<?php

return [
    ['title' => 'Analytics Dashboard','route' => 'analytics', 'icon' => 'mdi mdi-chart-line','permission' => 'dashboard.view'],
    [
        'title' => 'Users', 'icon' => 'mdi mdi-account-group','permission' => 'users.manage',
        'submenu' => [
            [
                'title' => 'User Lists','route' => 'admin.users.index','permission' => 'users.manage',
            ],
            [
                'title' => 'User Create','route' => 'admin.users.create','permission' => 'users.create',
            ],
           /*  [
                'title' => 'User Role Assign','route' => 'admin.users.assign','permission' => 'users.assign',
            ], */
        ],
    ],
    ['title' => 'Roles','route' => 'admin.roles.index','icon' => 'mdi mdi-settings','permission' => 'roles.manage',],
    ['title' => 'Settings','route' => 'admin.settings.index','icon' => 'mdi mdi-settings','permission' => 'settings.manage',],
];
