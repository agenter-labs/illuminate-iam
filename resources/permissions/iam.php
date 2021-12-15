<?php

return [

    /**
     * Control if all the iam tables should be truncated before running the seeder.
     */
    'roles' => [
        'administrator' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'user' => [
            'profile' => 'r,u',
        ],
        'standard' => [
            'profile' => 'r',
        ]
    ],

    /**
     * System permissions
     */
    'permissions' => [
        'users' => 'c,r,u,d',
        'profile' => 'r,u'
    ],
];