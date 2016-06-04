<?php

namespace Alegra\Tests;

use Alegra\Quote;

class QuoteTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals('estimates', Quote::resolvePath());
    }
}
