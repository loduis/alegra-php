<?php

namespace Alegra\Tests;

use Alegra\Company;

class CompanyTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals('company', Company::resolvePath());
    }

    public function testGet()
    {
        $company = Company::get();
        $this->assertArrayHasKey('name', $company);
        $this->assertArrayHasKey('identification', $company);
        $this->assertInstanceOf(Company::class, $company);
    }

    public function testSave()
    {
        $company = new Company;
        $name = Company::get()->name;
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
        $company = Company::get();
        $company->name = $this->faker->name;
        $company->save();
    }
}
