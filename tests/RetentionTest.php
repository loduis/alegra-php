<?php

namespace Alegra\Tests;

use Alegra\Retention;

class RetentionTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals('retentions', Retention::resolvePath());
    }

    public function testGetAll()
    {
        $retentions = Retention::all();
        $this->assertGreaterThanOrEqual(1, count($retentions));
    }
/*
    public function testFilterByType()
    {
        Retention::all(['type' => 'FUENTE', 'limit' => 2])->each(function ($retention) {
            $this->assertEquals('FUENTE', $retention->type);
        });
    }

    public function testTypes()
    {
        $retention = Retention::first();
        $this->assertInternalType('int', $retention->id);
        $this->assertInternalType('float', $retention->percentage);
        $this->assertInternalType('string', $retention->name);
        $this->assertInternalType('string', $retention->type);
        $this->assertInternalType('string', $retention->description);
    }
*/
}
