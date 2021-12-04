<?php

namespace Tests\Feature;

use Tests\TestCase;

use Database\Factories\CompanyFactory;

class CompanyTest extends TestCase
{
    public function testCreate()
    {
        $this->loginAs();
        $company = CompanyFactory::new()->create();
        $this->seeInDatabase('company', ['name' => $company['name']]);

        app('iam');
    }
}