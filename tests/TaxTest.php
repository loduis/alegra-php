<?php

namespace Alegra\Tests;

use Alegra\Tax;

class TaxTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals('taxes', Tax::resolvePath());
    }
}
