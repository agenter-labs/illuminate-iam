<?php

namespace Tests\Feature;

use Tests\TestCase;

use Database\Factories\PermissionFactory;
use Database\Factories\RoleFactory;

class RoleTest extends TestCase
{
    public function testCreate()
    {
        $role = RoleFactory::new()->create();
        $this->seeInDatabase('role', ['title' => $role['title']]);
    }

    public function testCheckPermission()
    {
        $permission = PermissionFactory::new()->permission('acme create')->create();
        $role = RoleFactory::new()->create();
        $role->permissions()->sync([$permission->id]);
        
        $this->assertTrue($role->hasPermission('acme-create'));

        $this->assertFalse($role->hasPermission('acme-edit'));
    }

    public function testAttachPermission()
    {
        $permission = PermissionFactory::new()->permission('acme create')->create();
        $role = RoleFactory::new()->create();
        $role->permissions()->sync([$permission->id]);
        
        $this->assertTrue($role->hasPermission('acme-create'));

        $this->assertFalse($role->hasPermission('acme-edit'));


        $permission = PermissionFactory::new()->permission('acme edit')->create();

        $role->attachPermission('acme-edit');

        $this->assertTrue($role->hasPermission('acme-edit'));


        $role->detachPermission('acme-edit');

        $this->assertFalse($role->hasPermission('acme-edit'));
    }
}