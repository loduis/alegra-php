<?php

namespace Alegra\Tests;

use Alegra\Item;

class ItemTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals('items', Item::resolvePath());
    }
}
