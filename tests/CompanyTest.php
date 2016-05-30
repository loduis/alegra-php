<?php

namespace Alegra\Tests;

use ReflectionClass;
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

    public function testAll()
    {
        $this->assertTrue((new ReflectionClass(Company::class))->getMethod('all')->isPrivate());
    }

    public function testDelete()
    {
        $this->assertTrue((new ReflectionClass(Company::class))->getMethod('delete')->isPrivate());
    }

    public function testInstanceCompany()
    {
        $company = new Company;

        $this->assertArrayHasKey('name', $company);
        $this->assertArrayHasKey('identification', $company);
        $this->assertInstanceOf(Company::class, $company);
    }
}
