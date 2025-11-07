<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Contexts (system-level contexts)
    |--------------------------------------------------------------------------
    */
        /* $permissionsConfig = config('app_permissions.user_contexts_layer', []);
        Log::info("permissions config - " . json_encode($permissionsConfig)); */
    //'user_contexts_layer' => [ ['id' =>1, 'value' => 'primary'], ['id' => 2, 'value' => 'secondary'], ['id' => 3 , 'value'=>'sub-secondary'], ['id' => 4, 'value' => 'tertiary']],
    'user_contexts_layer' => [1 => 'primary', 2 => 'secondary', 3 => 'sub-secondary', 4 => 'tertiary'],
        /* $userLevelId = 3;
        $contextValue = config("app_permissions.user_contexts_layer.{$userLevelId}");
        Log::info("permissions config value- " . $contextValue); */

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
                'create' => ['all_contexts' => false, 'contexts' => ['primary', 'secondary']],
                'edit'   => ['all_contexts' => false, 'contexts' => ['primary', 'secondary', 'sub-secondary']],
                'view'   => ['all_contexts' => true],
                'assign' => ['all_contexts' => false, 'contexts' => ['primary']],
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
