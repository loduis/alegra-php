<?php

namespace Alegra\Tests;

use Alegra\Tax;

class TaxTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals('taxes', Tax::resolvePath());
    }

    public function testGetAll()
    {
        $taxes = Tax::all();
        $this->assertGreaterThanOrEqual(1, count($taxes));
    }

    public function testFilterByType()
    {
        Tax::all(['type' => 'IVA', 'limit' => 2])->each(function ($tax) {
            $this->assertEquals('IVA', $tax->type);
        });
    }

    public function testTypes()
    {
        $tax = Tax::first();
        $this->assertInternalType('int', $tax->id);
        $this->assertInternalType('float', $tax->percentage);
        $this->assertInternalType('string', $tax->name);
        $this->assertInternalType('string', $tax->type);
        $this->assertInternalType('string', $tax->description);
    }
}
