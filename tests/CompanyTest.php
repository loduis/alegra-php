<?php

namespace Alegra\Tests;

use Alegra\Company;

class CompanyTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals('company', Company::resolvePath());
    }

    public function testFetch()
    {
        $company = Company::fetch();
        $this->assertArrayHasKey('name', $company);
        $this->assertArrayHasKey('identification', $company);
        $this->assertInstanceOf(Company::class, $company);
    }
}
