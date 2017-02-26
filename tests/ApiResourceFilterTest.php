<?php

use Illuminate\Api\Resource\Filter;

class SupportFluentTest extends PHPUnit_Framework_TestCase
{
    public function testAttributesAreSetByConstructor()
    {
        $array = ['name' => 'Taylor', 'age' => 25];
        $fluent = new Filter($array);
        $refl = new ReflectionObject($fluent);
        $attributes = $refl->getProperty('attributes');
        $attributes->setAccessible(true);
        $this->assertEquals($array, $attributes->getValue($fluent));
        $this->assertEquals($array, $fluent->getAttributes());
    }

    public function testAttributesAreSetByConstructorGivenStdClass()
    {
        $array = ['name' => 'Taylor', 'age' => 25];
        $fluent = new Filter((object) $array);
        $refl = new ReflectionObject($fluent);
        $attributes = $refl->getProperty('attributes');
        $attributes->setAccessible(true);
        $this->assertEquals($array, $attributes->getValue($fluent));
        $this->assertEquals($array, $fluent->getAttributes());
    }

    public function testAttributesAreSetByConstructorGivenArrayIterator()
    {
        $array = ['name' => 'Taylor', 'age' => 25];
        $fluent = new Filter(new FilterArrayIteratorStub($array));
        $refl = new ReflectionObject($fluent);
        $attributes = $refl->getProperty('attributes');
        $attributes->setAccessible(true);
        $this->assertEquals($array, $attributes->getValue($fluent));
        $this->assertEquals($array, $fluent->getAttributes());
    }

    public function testMagicMethodsCanBeUsedToSetAttributes()
    {
        $fluent = new Filter;
        $fluent->name = 'Taylor';
        $fluent->developer();
        $fluent->age(25);
        $this->assertEquals('Taylor', $fluent->name);
        $this->assertTrue($fluent->developer);
        $this->assertEquals(25, $fluent->age);
        $this->assertInstanceOf(Filter::class, $fluent->programmer());
    }

    public function testIssetMagicMethod()
    {
        $array = ['name' => 'Taylor', 'age' => 25];
        $fluent = new Filter($array);
        $this->assertTrue(isset($fluent->name));
        unset($fluent->name);
        $this->assertFalse(isset($fluent->name));
    }

    public function testToArrayReturnsAttribute()
    {
        $array = ['name' => 'Taylor', 'age' => 25];
        $fluent = new Filter($array);
        $this->assertEquals($array, $fluent->toArray());
    }

    public function testToJsonEncodes()
    {
        $array = ['name' => 'Taylor', 'age' => 25];
        $fluent = new Filter($array);
        $this->assertEquals('{"name":"Taylor","age":25}', $fluent->toJson());
    }
}

class FilterArrayIteratorStub implements IteratorAggregate
{
    protected $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = (array) $attributes;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->attributes);
    }
}
