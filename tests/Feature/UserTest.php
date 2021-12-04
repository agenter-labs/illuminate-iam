<?php

namespace Tests\Feature;

use Tests\TestCase;

use Database\Factories\UserFactory;
use Database\Factories\PermissionFactory;
use Database\Factories\RoleFactory;

class UserTest extends TestCase
{
    public function testCreate()
    {
        $user = UserFactory::new()->create();
        $this->seeInDatabase('user', ['email' => $user['email']]);
    }

    public function testCheckRole()
    {
        $user = UserFactory::new()->create();
        $permission = PermissionFactory::new()->permission('acme create')->create();
        $role = RoleFactory::new()->create();
        $role->permissions()->sync([$permission->id]);
        
        $user->roles()->attach($role->id, ['company_id' => 1]);
        
        $this->assertTrue($user->hasPermission('acme-create'));

        $this->assertFalse($user->hasPermission('acme-edit'));
    }

    public function testAttachRole()
    {
        $user = UserFactory::new()->create();
        $permission = PermissionFactory::new()->permission('acme create')->create();
        $role = RoleFactory::new()->create();
        $role->permissions()->sync([$permission->id]);
        
        $user->roles()->attach($role->id, ['company_id' => 1]);
        
        $this->assertTrue($user->hasPermission('acme-create'));

        $this->assertFalse($user->hasPermission('acme-edit'));

        $permission = PermissionFactory::new()->permission('acme edit')->create();
        $role = RoleFactory::new()->create();
        $role->permissions()->sync([$permission->id]);

        $user->attachRole($role, 2);

        $this->assertFalse($user->hasPermission('acme-edit', 1));
        $this->assertTrue($user->hasPermission('acme-edit', 2));
    }
}