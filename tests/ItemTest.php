<?php

namespace Alegra\Tests;

use Alegra\Tax;
use Alegra\Item;
use Alegra\Category;
use Alegra\Item\Price;
use Alegra\Item\Inventory;
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
        $this->assertInstanceOf(Collection::class, $item->price);
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

    public function testSaveWithTaxAttribute()
    {
        $tax  = Tax::first();
        $item = new Item;
        $item->name = $this->faker('name');
        $item->price = 5;
        $item->tax = $tax;
        $item->save();

        $this->assertInstanceOf(Collection::class, $item->price);
        $this->assertInstanceOf(Collection::class, $item->tax);

        $item->tax->each(function ($tax) {
            $this->assertInstanceOf(Tax::class, $tax);
            $this->assertInternalType('int', $tax->id);
        });
    }

    public function testSaveWithInventoryAttribute()
    {
        $item = new Item;
        $item->name = $this->faker('name');
        $item->price = 30;
        $item->inventory = [
            'unit' => 'centimeter',
            'cost' => 100,
            'initial' => 20
        ];
        $this->assertInstanceOf(Inventory::class, $item->inventory);
        $this->assertInternalType('float', $item->inventory->cost);
        $item->save();

        // Using chaining

        $item = new Item;
        $item->name = $this->faker('name');
        $item->price = 50;

        $item->inventory->unit = 'centimeter';
        $item->inventory->cost = 200;
        $item->inventory->initial = 5;
        $item->save();
        $this->assertInstanceOf(Inventory::class, $item->inventory);
        $this->assertInternalType('float', $item->inventory->cost);

        // Using full attributes

        $item = new Item;
        $item->name = $this->faker('name');
        $item->price = 50;

        $item->inventory->unit = 'centimeter';
        $item->inventory->unitCost = 200;
        $item->inventory->initialQuantity = 5;
        $item->save();
        $this->assertInstanceOf(Inventory::class, $item->inventory);
        $this->assertInternalType('float', $item->inventory->initialQuantity);
    }

    public function testAll()
    {
        $items = Item::all();
        $this->assertGreaterThanOrEqual(1, count($items));
        $items->each(function ($item) {
            $this->assertInstanceOf(Item::class, $item);
            $this->assertInternalType('int', $item->id);
            $this->assertInstanceOf(Collection::class, $item->price);
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
