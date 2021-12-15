<?php

return [

    'company' => [
        'enabled' => true
    ],

    /*------
    | Iam Models
    |--------------------------------------------------------------------------
    |
    | These are the models used by Iam to define the roles, permissions and teams.
    | If you want the Iam models to be in a different namespace or
    | to have a different name, you can do it here.
    |
    */
    'models' => [
        'user' => \AgenterLab\IAM\Models\User::class,

        'company' => \AgenterLab\IAM\Models\Company::class,

        'role' => \AgenterLab\IAM\Models\Role::class,

        'permission' => \AgenterLab\IAM\Models\Permission::class,
    ],

    /*------------------
    |
    | These are the tables store all the authorization data.
    |
    */
    'tables' => [

        'user' => 'user',

        'company' => 'company',

        'role' => 'role',

        'permission' => 'permission',

        'role_permission' => 'role_permission',

        'user_company' => 'user_company',

        'user_role' => 'user_role',
    ],

    /*
    |--------------------------------------------------------------------------
    | Iam Foreign Keys
    |--------------------------------------------------------------------------
    |
    | These are the foreign keys used by iam in the intermediate tables.
    |
    */
    'foreign_keys' => [
        /**
         * User foreign key on Iam's role_user and permission_user tables.
         */
        'company' => 'company_id',

        /**
         * User foreign key on Iam's role_user and permission_user tables.
         */
        'user' => 'user_id',

        /**
         * Role foreign key on Iam's role_user and permission_role tables.
         */
        'role' => 'role_id',

        /**
         * Role foreign key on Iam's permission_user and permission_role tables.
         */
        'permission' => 'permission_id'
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Manage IAM's cache configurations. It uses the driver defined in the
    | config/cache.php file.
    |
    */
    'cache' => [
        /*
        |--------------------------------------------------------------------------
        | Use cache in the package
        |--------------------------------------------------------------------------
        |
        | Defines if IAM will use Laravel's Cache to cache the roles and permissions.
        | NOTE: Currently the database check does not use cache.
        |
        */
        'enabled' => env('IAM_ENABLE_CACHE', true),

        /*
        |--------------------------------------------------------------------------
        | Time to store in cache IAM's roles and permissions.
        |--------------------------------------------------------------------------
        |
        | Determines the time in SECONDS to store IAM's roles and permissions in the cache.
        |
        */

        'expiration_time' => 3600,
        /*
        |
        | Default Cache Store
        |
        */
        'store' => env('IAM_CACHE_STORE', 'file'),
    ],

    /**
     * Scope
     */
    'scope' => [
        'skip_tables' => [],
        'column' => 'company_id'
    ],

    'permissions_as_gates' => false
];

