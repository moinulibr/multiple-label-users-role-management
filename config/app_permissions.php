<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Contexts (system-level contexts)
    |--------------------------------------------------------------------------
    */
        /* $permissionsConfig = config('app_permissions.user_contexts_layer', []);
        Log::info("permissions config - " . json_encode($permissionsConfig)); */
    'user_contexts_layer' => ['primary', 'secondary', 'sub-secondary', 'tertiary'],

    /*
    |--------------------------------------------------------------------------
    | Modules & Actions with Context Rules
    |--------------------------------------------------------------------------
    | - all_contexts = true হলে সব contexts allowed
    | - contexts = [] দিলে specific contexts allowed
    |--------------------------------------------------------------------------
    */

    'modules' => [

        'users' => [
            'actions' => [
                'manage' => ['all_contexts' => true],
                'assign' => ['all_contexts' => false, 'contexts' => ['primary']],
                'create' => ['all_contexts' => false, 'contexts' => ['primary', 'secondary']],
                'edit'   => ['all_contexts' => false, 'contexts' => ['primary', 'secondary', 'sub-secondary']],
                'view'   => ['all_contexts' => true],
            ],
        ],

        'roles' => [
            'actions' => [
                'manage' => ['all_contexts' => true],
                'assign' => ['all_contexts' => true],
            ],
        ],

        'settings' => [
            'actions' => [
                'manage' => ['all_contexts' => true],
                'view'   => ['all_contexts' => true],
                'update' => ['all_contexts' => true],
            ],
        ],
    ],
];
