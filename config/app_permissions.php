<?php

return [

    //configured in UserContextManager.php
    //app_permissions.user_contexts_layer setting
    'fixedUserType' => [1 => 'super_admin', 2 => 'admin',3=>'owner'],

    'contextLayerSettings' => [],



    /*
    |--------------------------------------------------------------------------
    | User Contexts (system-level contexts)
    |--------------------------------------------------------------------------
    */

    /* $permissionsConfig = config('app_permissions.user_contexts_layer', []);
    Log::info("permissions config - " . json_encode($permissionsConfig)); */
    //'user_contexts_layer' => [ ['id' =>1, 'value' => 'primary'], ['id' => 2, 'value' => 'secondary'], ['id' => 3 , 'value'=>'sub-secondary'], ['id' => 4, 'value' => 'tertiary']],
    'user_contexts_layer' => [1 => 'primary', 2 => 'secondary', 3 => 'sub-secondary', 4 => 'tertiary'],//nullableBusinessOfUserProifle
    /* $userLevelId = 3;
    $contextValue = config("app_permissions.user_contexts_layer.{$userLevelId}");
    Log::info("permissions config value- " . $contextValue); */

    /*
    |--------------------------------------------------------------------------
    | Modules & Actions with Context Rules
    |--------------------------------------------------------------------------
    | - isAllowedToAllContextLayer = true হলে সব contexts allowed
    | - contexts = [] দিলে specific contexts allowed
    |--------------------------------------------------------------------------
    */
    //isAllowedToAllContextLayer
    'modules' => [

        'users' => [
            'actions' => [
                'manage' => ['isAllowedToAllContextLayer' => true],
                'create' => ['isAllowedToAllContextLayer' => false, 'contextLayer' => ['primary', 'secondary']],
                'edit'   => ['isAllowedToAllContextLayer' => false, 'contextLayer' => ['primary', 'secondary', 'sub-secondary']],
                'view'   => ['isAllowedToAllContextLayer' => true],
                'assign' => ['isAllowedToAllContextLayer' => false, 'contextLayer' => ['primary']],
            ],
        ],

        'roles' => [
            'actions' => [
                'manage' => ['isAllowedToAllContextLayer' => true],
                'assign' => ['isAllowedToAllContextLayer' => true],
            ],
        ],

        'settings' => [
            'actions' => [
                'manage' => ['isAllowedToAllContextLayer' => true],
                'view'   => ['isAllowedToAllContextLayer' => true],
                'update' => ['isAllowedToAllContextLayer' => true],
            ],
        ],
    ],
];
