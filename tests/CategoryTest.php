<?php

namespace Alegra\Tests;

use Alegra\Category;

class CategoryTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals('categories', Category::resolvePath());
    }
}
