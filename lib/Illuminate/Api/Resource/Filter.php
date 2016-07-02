<?php

namespace Illuminate\Api\Resource;

use ArrayAccess;
use JsonSerializable;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\AttributeAccess;
use Illuminate\Support\Traits\AttributeCastable;
use Illuminate\Support\Traits\AttributeFillable;
use Illuminate\Support\Traits\AttributeSerialize;

class Filter implements ArrayAccess, Arrayable, Jsonable, JsonSerializable
{

    /**
     * Indicates whether attributes are snake cased on arrays.
     *
     * @var bool
     */
    public static $snakeAttributes = false;

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
     * Add ability for casting attributes
     */
    use AttributeCastable;

    /**
     * Add ability for convert to json
     */
    use AttributeSerialize;

    /**
     * Add ability for mass assignment attribute
     */
    use AttributeFillable;

    /**
     * Create a new instance if the value isn't one already.
     *
     * @param  mixed  $parameters
     * @return static
     */
    public static function make($parameters = [])
    {
        if ($parameters instanceof static) {
            return $parameters;
        }

        return new static($parameters);
    }

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array|object  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * Set a given attribute on the parameter.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $key = $this->snakeAttribute($key);

        $this->validateFillable($key);

        $this->attributes[$key] = $this->castSetAttribute($key, $value);

        return $this;
    }

    /**
     * Get an attribute from the parameter.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $key = $this->snakeAttribute($key);
        if (array_key_exists($key, $this->attributes)) {
            $value = $this->attributes[$key];

            // If the attribute exists within the cast array, we will convert it to
            // an appropriate native PHP type dependant upon the associated value
            // given with the key in the pair. Dayle made this comment line up.
            if ($this->hasCast($key)) {
                return $this->castAttribute($key, $value);
            }

            return $value;
        }
    }

    /**
     * Convert the params's attributes to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $attributes = [];

        foreach ($this->attributes as $key => $value) {
            // If the values implements the Arrayable interface we can just call this
            // toArray method on the instances which will convert both models and
            // collections to their proper array form and we'll set the values.
            if ($value instanceof Arrayable) {
                $value = $value->toArray();
            } elseif ($value === true) { // fix bool for query string
                $value = 'true';
            } elseif ($value === false) { // fix bool for query string
                $value = 'false';
            }
            $attributes[$key] = $value;
        }

        return $attributes;
    }

    /**
     * Get all items except for those with the specified keys.
     *
     * @param  mixed  $keys
     * @return static
     */
    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        return Arr::except($this->attributes, $keys);
    }

    /**
     * Handle dynamic calls to the container to set attributes.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        $key = $this->snakeAttribute($method);
        $value = count($parameters) > 0 ? $parameters[0] : true;

        $this->setAttribute($key, $value);

        return $this;
    }

    protected function snakeAttribute($attribute)
    {
        if (static::$snakeAttributes) {
            $attribute = Str::snake($attribute);
        }

        return $attribute;
    }
}
