<?php

namespace Alegra\Tests;

use Alegra\Tax;
use Alegra\Item;
use Alegra\Price;
use Alegra\Category;
use Alegra\PriceList;
use Illuminate\Api\Resource\Collection;

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

    /**
     * @expectedException     \GuzzleHttp\Exception\ClientException
     * @expectedExceptionCode 400
     */
    public function testSaveFailWhenNotPriceIsSet()
    {
        $item = new Item;
        $item->name = $this->faker('name');
        $item->save();
    }

    /**
     * @expectedException     \GuzzleHttp\Exception\ClientException
     * @expectedExceptionCode 400
     */
    public function testSaveFailWhenRequestIsEmpty()
    {
        $item = new Item;
        $item->save();
    }

    public function itestSaveWithTax()
    {
        $item = new Item;
        $item->name = $this->faker('name');
        $item->price = 5;
        $item->tax = 1;
        $item->save();
        $this->assertInstanceOf(PriceList::class, $item->price);
        $this->assertInstanceOf(Collection::class, $item->tax);

        $item->tax->each(function ($tax) {
            $this->assertInstanceOf(Tax::class, $tax);
            $this->assertInternalType('int', $tax->id);
        });
    }

    public function testAll()
    {
        $items = Item::all();
        $this->assertGreaterThanOrEqual(1, count($items));
        $items->each(function ($item) {
            $this->assertInstanceOf(Item::class, $item);
            $this->assertInternalType('int', $item->id);
            $this->assertInstanceOf(PriceList::class, $item->price);
        });
        $this->assertInstanceOf(Collection::class, $items);
    }

    public function testGet()
    {
        $item   = Item::create(['name' => $name = $this->faker('name'), 'price' => 10]);
        $createdItem = Item::get($item->id);
        $this->assertSame($name, $createdItem->name);
    }

    public function testDelete()
    {
        $item   = Item::create(['name' => $this->faker('name'), 'price' => 10]);
        $item->delete();
        $this->assertSame(null, $item->id);
    }
}
