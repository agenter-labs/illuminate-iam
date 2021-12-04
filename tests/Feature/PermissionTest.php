<?php

namespace Tests\Feature;

use Tests\TestCase;

use Database\Factories\PermissionFactory;

class PermissionTest extends TestCase
{
    public function testCreate()
    {
        $permission = PermissionFactory::new()->create();
        $this->seeInDatabase('permission', ['title' => $permission['title']]);
    }
}