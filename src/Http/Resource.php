<?php

namespace Alegra\Http;

use BadMethodCallException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;

abstract class Resource extends Fluent
{
    /**
     * Create a new instance of resource
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct(static::transforms($attributes));
    }

    protected static function instanceFromRequest($method, $id, $params = [])
    {
        $response = static::request($method, $id, $params);

        if (Arr::isAssoc($response)) {
            return new static($response);
        }

        $rows = [];

        foreach ($response  as $line) {
            $rows[] = new static($line);
        }

        return $rows;
    }

    /**
     * Invoke the request of the current resource
     *
     * @param  string $method
     * @param  null|int|string $id
     * @param  array|Arrayable $params
     * @return array
     */
    protected static function request($method, $id = null, $params = [])
    {
        $path = static::resolvePath()  . ($id ? "/$id" : '');

        return Client::request($method, $path, $params);
    }

    /**
     * Resolve the path of the resource for call request.
     *
     * @return string
     */
    public static function resolvePath()
    {
        static $resolved = [];

        $class = static::class;

        if (($path = Arr::get($resolved, $class)) !== null) {
            return $path;
        }

        if (static::propertyExists('path')) {
            return $resolved[$class] = static::$path;
        }

        // Useful for namespaces: Foo\Charge
        $path = $class;
        if ($postfixNamespaces = strrchr($path, '\\')) {
            $path = substr($postfixNamespaces, 1);
        }

        $path = Str:: snake($path, '-');
        $path = Str::plural($path);

        return $resolved[$class] =  $path;
    }

    /**
     * Transforms the specified attributes on the property $transforms
     * to resource objects
     *
     * @param  array $attributes
     * @return array
     */
    protected static function transforms($attributes)
    {
        if (static::propertyExists('transforms')) {
            foreach (static::$transforms as $field => $transformClass) {
                if (Arr::has($attributes, $field)) {
                    $attributes[$field] = new $transformClass($attributes[$field]);
                }
            }
        }

        return $attributes;
    }

    /**
     * Check if exists a property in the current resource
     *
     * @param  string $property
     * @return bool
     */
    protected static function propertyExists($property)
    {
        return property_exists(static::class, $property);
    }

    /**
     * Combine attributes in the current resource object
     *
     * @param  array $attributes
     * @return void
     */
    protected function combine($attributes)
    {
        $this->attributes = array_merge($this->toArray(), static::transforms($attributes));

        return $this;
    }
}
