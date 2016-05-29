<?php

namespace Alegra\Tests;

use Alegra\Retention;

class RetentionTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals('retentions', Retention::resolvePath());
    }
}
