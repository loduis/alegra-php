<?php

namespace Alegra\Tests\Http\Eloquent;

use StdClass;
use Carbon\Carbon;
use Alegra\Tests\TestCase;
use Alegra\Http\Eloquent\Model;


class ModelTest extends TestCase
{
    public function tearDown()
    {
        parent::tearDown();
        Carbon::resetToStringFormat();
    }

    public function itestAttributeManipulation()
    {
        $model = new HttpResourceModelStub;
        $model->name = 'foo';
        $this->assertEquals('foo', $model->name);
        $this->assertTrue(isset($model->name));
        unset($model->name);
        $this->assertFalse(isset($model->name));

        // test mutation
        $model->list_items = ['name' => 'taylor'];
        $this->assertEquals(['name' => 'taylor'], $model->list_items);
        $attributes = $model->getAttributes();
        $this->assertEquals(json_encode(['name' => 'taylor']), $attributes['list_items']);
    }

    public function itestModelAttributesAreCastedWhenPresentInCastsArray()
    {
        $model = new HttpResourceModelCastingStub;
        $model->setDateFormat('Y-m-d H:i:s');
        $model->intAttribute = '3';
        $model->floatAttribute = '4.0';
        $model->stringAttribute = 2.5;
        $model->boolAttribute = 1;
        $model->booleanAttribute = 0;
        $model->objectAttribute = ['foo' => 'bar'];
        $obj = new StdClass;
        $obj->foo = 'bar';
        $model->arrayAttribute = $obj;
        $model->jsonAttribute = ['foo' => 'bar'];
        $model->dateAttribute = '1969-07-20';
        $model->datetimeAttribute = '1969-07-20 22:56:00';
        $model->timestampAttribute = '1969-07-20 22:56:00';
        $this->assertInternalType('int', $model->intAttribute);
        $this->assertInternalType('float', $model->floatAttribute);
        $this->assertInternalType('string', $model->stringAttribute);
        $this->assertInternalType('boolean', $model->boolAttribute);
        $this->assertInternalType('boolean', $model->booleanAttribute);
        $this->assertInternalType('object', $model->objectAttribute);
        $this->assertInternalType('array', $model->arrayAttribute);
        $this->assertInternalType('array', $model->jsonAttribute);
        $this->assertTrue($model->boolAttribute);
        $this->assertFalse($model->booleanAttribute);
        $this->assertEquals($obj, $model->objectAttribute);
        $this->assertEquals(['foo' => 'bar'], $model->arrayAttribute);
        $this->assertEquals(['foo' => 'bar'], $model->jsonAttribute);
        $this->assertEquals('{"foo":"bar"}', $model->jsonAttributeValue());
        $this->assertInstanceOf('Carbon\Carbon', $model->dateAttribute);
        $this->assertInstanceOf('Carbon\Carbon', $model->datetimeAttribute);
        $this->assertEquals('1969-07-20', $model->dateAttribute->toDateString());
        $this->assertEquals('1969-07-20 22:56:00', $model->datetimeAttribute->toDateTimeString());
        $this->assertEquals(-14173440, $model->timestampAttribute);
        $arr = $model->toArray();
        $this->assertInternalType('int', $arr['intAttribute']);
        $this->assertInternalType('float', $arr['floatAttribute']);
        $this->assertInternalType('string', $arr['stringAttribute']);
        $this->assertInternalType('boolean', $arr['boolAttribute']);
        $this->assertInternalType('boolean', $arr['booleanAttribute']);
        $this->assertInternalType('object', $arr['objectAttribute']);
        $this->assertInternalType('array', $arr['arrayAttribute']);
        $this->assertInternalType('array', $arr['jsonAttribute']);
        $this->assertTrue($arr['boolAttribute']);
        $this->assertFalse($arr['booleanAttribute']);
        $this->assertEquals($obj, $arr['objectAttribute']);
        $this->assertEquals(['foo' => 'bar'], $arr['arrayAttribute']);
        $this->assertEquals(['foo' => 'bar'], $arr['jsonAttribute']);
        $this->assertEquals('1969-07-20 00:00:00', $arr['dateAttribute']);
        $this->assertEquals('1969-07-20 22:56:00', $arr['datetimeAttribute']);
        $this->assertEquals(-14173440, $arr['timestampAttribute']);
    }

    public function itestModelAttributeCastingPreservesNull()
    {
        $model = new HttpResourceModelStub;
        $model->intAttribute = null;
        $model->floatAttribute = null;
        $model->stringAttribute = null;
        $model->boolAttribute = null;
        $model->booleanAttribute = null;
        $model->objectAttribute = null;
        $model->arrayAttribute = null;
        $model->jsonAttribute = null;
        $model->dateAttribute = null;
        $model->datetimeAttribute = null;
        $model->timestampAttribute = null;
        $attributes = $model->getAttributes();
        $this->assertNull($attributes['intAttribute']);
        $this->assertNull($attributes['floatAttribute']);
        $this->assertNull($attributes['stringAttribute']);
        $this->assertNull($attributes['boolAttribute']);
        $this->assertNull($attributes['booleanAttribute']);
        $this->assertNull($attributes['objectAttribute']);
        $this->assertNull($attributes['arrayAttribute']);
        $this->assertNull($attributes['jsonAttribute']);
        $this->assertNull($attributes['dateAttribute']);
        $this->assertNull($attributes['datetimeAttribute']);
        $this->assertNull($attributes['timestampAttribute']);
        $this->assertNull($model->intAttribute);
        $this->assertNull($model->floatAttribute);
        $this->assertNull($model->stringAttribute);
        $this->assertNull($model->boolAttribute);
        $this->assertNull($model->booleanAttribute);
        $this->assertNull($model->objectAttribute);
        $this->assertNull($model->arrayAttribute);
        $this->assertNull($model->jsonAttribute);
        $this->assertNull($model->dateAttribute);
        $this->assertNull($model->datetimeAttribute);
        $this->assertNull($model->timestampAttribute);
        $array = $model->toArray();
        $this->assertNull($array['intAttribute']);
        $this->assertNull($array['floatAttribute']);
        $this->assertNull($array['stringAttribute']);
        $this->assertNull($array['boolAttribute']);
        $this->assertNull($array['booleanAttribute']);
        $this->assertNull($array['objectAttribute']);
        $this->assertNull($array['arrayAttribute']);
        $this->assertNull($array['jsonAttribute']);
        $this->assertNull($array['dateAttribute']);
        $this->assertNull($array['datetimeAttribute']);
        $this->assertNull($array['timestampAttribute']);
    }

    public function itestGetMutatedAttributes()
    {
        $model = new HttpResourceModelGetMutatorsStub;

        $this->assertEquals(['first_name', 'middle_name', 'last_name'], $model->getMutatedAttributes());

        HttpResourceModelGetMutatorsStub::resetMutatorCache();

        HttpResourceModelGetMutatorsStub::$snakeAttributes = false;
        $this->assertEquals(['firstName', 'middleName', 'lastName'], $model->getMutatedAttributes());
    }

    public function testToArrayUsesMutators()
    {
        HttpResourceModelStub::$snakeAttributes = true;

        $model = new HttpResourceModelStub;
        $model->list_items = [1, 2, 3];
        $array = $model->toArray();
        $this->assertEquals([1, 2, 3], $array['list_items']);
    }
}

class HttpResourceModelStub extends Model
{
    public function getListItemsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setListItemsAttribute($value)
    {
        $this->attributes['list_items'] = json_encode($value);
    }

    public function getPasswordAttribute()
    {
        return '******';
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password_hash'] = sha1($value);
    }
}

class HttpResourceModelCamelStub extends Model
{
    public static $snakeAttributes = false;
}

class HttpResourceModelGetMutatorsStub extends Model
{
    public static function resetMutatorCache()
    {
        static::$mutatorCache = [];
    }

    public function getFirstNameAttribute()
    {
    }

    public function getMiddleNameAttribute()
    {
    }

    public function getLastNameAttribute()
    {
    }

    public function doNotgetFirstInvalidAttribute()
    {
    }

    public function doNotGetSecondInvalidAttribute()
    {
    }

    public function doNotgetThirdInvalidAttributeEither()
    {
    }

    public function doNotGetFourthInvalidAttributeEither()
    {
    }
}

class HttpResourceModelCastingStub extends Model
{
    protected $casts = [
        'intAttribute' => 'int',
        'floatAttribute' => 'float',
        'stringAttribute' => 'string',
        'boolAttribute' => 'bool',
        'booleanAttribute' => 'boolean',
        'objectAttribute' => 'object',
        'arrayAttribute' => 'array',
        'jsonAttribute' => 'json',
        'dateAttribute' => 'date',
        'datetimeAttribute' => 'datetime',
        'timestampAttribute' => 'timestamp',
    ];

    public function jsonAttributeValue()
    {
        return $this->attributes['jsonAttribute'];
    }
}
