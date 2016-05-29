<?php

namespace Alegra\Tests;

use Alegra\Company;

class CompanyTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals('company', Company::resolvePath());
    }
}
