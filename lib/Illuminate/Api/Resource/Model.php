<?php

namespace Illuminate\Api\Resource;

use ArrayAccess;
use JsonSerializable;
use DateTimeInterface;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\AttributeAccess;
use Illuminate\Support\Traits\AttributeMutator;
use Illuminate\Support\Traits\AttributeCastable;
use Illuminate\Support\Traits\AttributeSerialize;
use Illuminate\Support\Traits\AttributeTransformer;

class Model implements ArrayAccess, Arrayable, Jsonable, JsonSerializable
{
    /**
     * Indicates whether attributes are snake cased on arrays.
     *
     * @var bool
     */
    public static $snakeAttributes = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The cast key type
     *
     * @var string
     */
    protected static $castKey = 'int';

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * Add ability for access attributes
     */
    use AttributeAccess;

    /**
     * Add ability for mutate attributes
     */
    use AttributeMutator;

    /**
     * Add ability for casting attributes
     */
    use AttributeCastable;

    /**
     * Add ability for transform attributes
     */
    use AttributeTransformer;

    /**
     * Add ability for convert to json
     */
    use AttributeSerialize;

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function fill(array $attributes = [])
    {
        $this->attributes = [];

        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Get the value of the resource's primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->getAttribute($this->getKeyName());
    }

    /**
     * Set the value of the resource's primary key.
     *
     * @param mixed $value
     * @return $this
     */
    public function setKey($value)
    {
        $this->attributes[$this->getKeyName()] = $value;

        return $this;
    }

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    /**
     * Set the primary key for the model.
     *
     * @param  string  $key
     * @return $this
     */
    public function setKeyName($key)
    {
        $this->primaryKey = $key;

        return $this;
    }

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $attributes = $this->getArrayableAttributes();

        return $this->attributesToArray($attributes);
    }

    /**
     * Export visible attributes that are used in the transform process
     *
     * @return array
     */
    public function toArrayVisible()
    {
        $attributes = $this->getArrayableVisibleAttributes();

        return $this->attributesToArray($attributes);
    }

    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = $this->getAttributeFromArray($key);

        // If the attribute has a transform, we will call that then return what
        // it returns the value, which is useful for transforming values
        // from plain types to Model type.
        // If value is null, this method create of base type
        if ($this->hasTransformer($key) && $value === null) {
            return $this->transformAttribute($key, [])->getAttributes()[$key];
        }

        // If the attribute has a get mutator, we will call that then return what
        // it returns as the value, which is useful for transforming values on
        // retrieval from the model to a form that is more useful for usage.
        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $value);
        }

        // If the attribute exists within the cast array, we will convert it to
        // an appropriate native PHP type dependant upon the associated value
        // given with the key in the pair. Dayle made this comment line up.
        if ($this->hasCast($key)) {
            return $this->castAttribute($key, $value);
        }

        return $value;
    }

    /**
     * Get an attribute from the $attributes array.
     *
     * @param  string  $key
     * @return mixed
     */
    protected function getAttributeFromArray($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
    }

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    protected function attributesToArray($values)
    {
        $attributes = [];

        foreach ($values as $key => $value) {
            // If the values implements the Arrayable interface we can just call this
            // toArray method on the instances which will convert both models and
            // collections to their proper array form and we'll set the values.
            if ($value instanceof Collection) {
                $value = $value->allVisible();
            } elseif ($value instanceof self) {
                $value = $value->toArrayVisible();
            } elseif ($value instanceof Arrayable) {
                $value = $value->toArray();
            }
            $attributes[$key] = $value;
        }

        $mutatedAttributes = $this->mutateAttributes($attributes);

        return $this->castAttributes($attributes, $mutatedAttributes);
    }

    /**
     * Get an attribute array of all arrayable attributes.
     *
     * @return array
     */
    protected function getArrayableAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get an attribute array of all arrayable values.
     *
     * @return array
     */
    protected function getArrayableVisibleAttributes()
    {
        $visible   = static::getStaticProperty('visible', []);
        $visible[] = $this->primaryKey;
        $visible   = array_unique($visible);

        return array_intersect_key($this->getArrayableAttributes(), array_flip($visible));
    }

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        // First we will check for the presence of a mutator for the set operation
        // which simply lets the developers tweak the attribute as it is set on
        // the model, such as "json_encoding" an listing of data for storage.
        if ($this->hasTransformer($key)) {
            return $this->transformAttribute($key, $value);
        }

        if ($this->hasSetMutator($key)) {
            return $this->mutatingAttribute($key, $value);
        }

        if ($value && $this->isDateCastable($key)) {
            // If an attribute is listed as a "date", we'll convert it from a DateTime
            // instance into a form proper for storage on the database tables using
            // the connection grammar's date format. We will auto set the values.
            $value = $this->fromDateTime($value);
        }

        if ($value !== null && $this->isJsonCastable($key)) {
            $value = $this->asJson($value);
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Convert a DateTime to a storable string.
     *
     * @param  \DateTime|int  $value
     * @return string
     */
    public function fromDateTime($value)
    {
        $format = $this->getDateFormat();

        $value = $this->asDateTime($value);

        return $value->format($format);
    }

    /**
     * Set the date format used by the model.
     *
     * @param  string  $format
     * @return $this
     */
    public function setDateFormat($format)
    {
        $this->dateFormat = $format;

        return $this;
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->getDateFormat());
    }

    /**
     * Get the format for database stored dates.
     *
     * @return string
     */
    protected function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * Get a static property from Model or $defualt if not exists
     *
     * @param  string $name
     * @param  mixed $default
     * @return mixed
     */
    protected static function getStaticProperty($name, $default = null)
    {
        return property_exists(static::class, $name) ? static::$$name : $default;
    }

    /**
     * Get the collection handler transform.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getCollectionTransformerHandler()
    {
        return Collection::class;
    }
}
