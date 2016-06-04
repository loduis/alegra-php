<?php

namespace Alegra\Tests;

use Alegra\Company;
use GuzzleHttp\Exception\ClientException;

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
        $this->assertPrivate(Company::class, 'all');
    }

    public function testDelete()
    {
        $this->assertPrivate(Company::class, 'delete');
    }

    public function testSave()
    {
        $company = new Company;
        $name = Company::fetch()->name;
        $company->name = $this->faker->name;
        $company->address = [
            'address' => 'Cambia la dir',
            'city' => 'bogota'
        ];
        $company->save();
        $this->assertNotEquals($name, $company->name);
    }

    /**
     * I think that's a bug
     *
     * @return void
     */
    public function testSaveFullResource()
    {
        $company = Company::fetch();
        $company->name = $this->faker->name;
        $company->save();
    }
}
