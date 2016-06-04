<?php

namespace Alegra\Tests;

use Alegra\Seller;

class SellerTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals('sellers', Seller::resolvePath());
    }
}
