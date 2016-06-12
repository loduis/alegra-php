<?php

namespace Illuminate\Api\Resource;

use ArrayAccess;
use JsonSerializable;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\AttributeAccess;
use Illuminate\Support\Traits\AttributeSerialize;

class Parameter implements ArrayAccess, Arrayable, Jsonable, JsonSerializable
{

    /**
     * Indicates whether attributes are snake cased on arrays.
     *
     * @var bool
     */
    public static $snakeAttributes = false;

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Add ability for access attributes
     */
    use AttributeAccess;

    /**
     * Add ability for convert to json
     */
    use AttributeSerialize;

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

        $this->attributes[$key] = $value;

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
            return $this->attributes[$key];
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
            }
            $attributes[$key] = $value;
        }

        return $attributes;
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
        $method = $this->snakeAttribute($method);

        $this->attributes[$method] = count($parameters) > 0 ? $parameters[0] : true;

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
