<?php

namespace Illuminate\Support\Traits;

trait AttributeAccess
{
    /**
     * The container's attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Get all of the current attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set a given attribute on the container.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    abstract public function setAttribute($key, $value);

    /**
     * Get an attribute from the container.
     *
     * @param  string  $key
     * @return mixed
     */
    abstract public function getAttribute($key);

    /**
     * Determine if an attribute exists on the container.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasAttribute($key)
    {
        return $this->getAttribute($key) !== null;
    }

    /**
     * Dynamically retrieve attributes on the container.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the container.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Determine if an attribute exists on the container.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->hasAttribute($key);
    }

    /**
     * Unset an attribute on the container.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
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
        return static::staticPropertyExists($name) ? static::$$name : $default;
    }

    /**
     * Check if exists a property in the current resource
     *
     * @param  string $property
     * @return bool
     */
    protected static function staticPropertyExists($property)
    {
        return property_exists(static::class, $property);
    }

    /**
     * Attribute for debug info
     *
     * @return array
     */
    public function __debugInfo()
    {
        return $this->getAttributes();
    }
}
