<?php

namespace Tests;

use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Database\Factories\UserFactory;
use AgenterLab\IAM\Models\User;


abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations;

    protected function setUp(): void
    {    
        parent::setUp();
        UserFactory::new()->create();
        config([
            'app.instance_id' => 1,
            'auth.guards.api.driver' => 'token',
            'auth.providers.users.driver' => 'eloquent',
            'auth.providers.users.model' => User::class
        ]);
    }

    protected function loginAs()
    {
        $user = User::first();
        return $this->actingAs($user,'api');
    }
    
}
