<?php

namespace Alegra\Tests;

use Alegra\Item;
use Alegra\Price;
use Alegra\Category;
use Alegra\PriceList;

class ItemTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals('items', Item::resolvePath());
    }

    public function testCreate()
    {
        $item = Item::create([
            'name' => $this->faker('name'),
            'price' => $this->faker->randomNumber(2)
        ]);

        $this->assertInstanceOf(Item::class, $item);
        $this->assertInternalType('int', $item->id);
        $this->assertInternalType('string', $item->name);

        // Assert category attribute
        $this->assertInstanceOf(Category::class, $item->category);
        $this->assertInternalType('int', $item->category->id);
        $this->assertInternalType('string', $item->category->name);

        // Assert price attribute
        $this->assertInstanceOf(PriceList::class, $item->price);
        $this->assertGreaterThanOrEqual(1, count($item->price));
        $item->price->each(function ($price) {
            $this->assertInstanceOf(Price::class, $price);
            $this->assertInternalType('int', $price->id);
            $this->assertInternalType('int', $price->idPriceList);
            $this->assertInternalType('string', $price->name);
            $this->assertInternalType('float', $price->price);
        });
    }

    public function testAll()
    {
        //$items = Item::all();
        //print_r($items);
    }
}
