<?php

namespace Alegra\Tests;

use Alegra\Category;

class CategoryTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals('categories', Category::resolvePath());
    }

    public function testGetAll()
    {
        $categories = Category::all();
        $this->assertGreaterThanOrEqual(1, count($categories));
    }

    public function testFilterByType()
    {
        Category::all(['type' => Category::TYPE_INCOME, 'limit' => 2])->each(function ($category) {
            $this->assertEquals(Category::TYPE_INCOME, $category->type);
        });
    }

    public function testTypes()
    {
        $category = Category::first();
        $this->assertInternalType('int', $category->id);
        $this->assertInternalType('int', $category->idParent);
        $this->assertInternalType('string', $category->name);
        $this->assertInternalType('string', $category->type);
        $this->assertInternalType('string', $category->description);
        $this->assertInternalType('bool', $category->readOnly);
    }
}
