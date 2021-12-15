<?php

namespace Tests\Console;

use Tests\TestCase;
use AgenterLab\IAM\Models\Permission;
use AgenterLab\IAM\Models\Role;

class PermissionSeedTest extends TestCase
{
    public $mockConsoleOutput = true;


    public function testPermissionSeed()
    {
        $result = $this->artisan('iam:permission-seed iam');

        $this->assertTrue($result == 0);

    }
}