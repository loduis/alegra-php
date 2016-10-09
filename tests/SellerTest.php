<?php

namespace Alegra\Tests;

use Alegra\Seller;

class SellerTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals('sellers', Seller::resolvePath());
    }

    public function testGetAll()
    {
        $sellers = Seller::all();
        $this->assertGreaterThanOrEqual(1, count($sellers));
    }

    public function testTypes()
    {
        $seller = Seller::first();
        $this->assertInternalType('int', $seller->id);
        $this->assertInternalType('string', $seller->identification);
        $this->assertInternalType('string', $seller->name);
        $this->assertInternalType('string', $seller->status);
        $this->assertInternalType('string', $seller->observations);
    }
}
