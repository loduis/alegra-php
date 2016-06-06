<?php

namespace Alegra\Tests;

use Alegra\Company;
use GuzzleHttp\Exception\ClientException;

class CompanyTest extends TestCase
{
    public function itestResolvePath()
    {
        $this->assertEquals('company', Company::resolvePath());
    }

    public function itestFetch()
    {
        $company = Company::fetch();
        $this->assertArrayHasKey('name', $company);
        $this->assertArrayHasKey('identification', $company);
        $this->assertInstanceOf(Company::class, $company);
    }

    public function itestAll()
    {
        $this->assertPrivate(Company::class, 'all');
    }

    public function itestDelete()
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
    public function itestSaveFullResource()
    {
        $company = Company::fetch();
        $company->name = $this->faker->name;
        $company->save();
    }
}
