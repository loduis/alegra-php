<?php

namespace Alegra\Http;

use BadMethodCallException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

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

    /**
     * Fetch all resources
     *
     * @param  array|Arrayable $params
     * @return array
     */
    public static function all($params = [])
    {
        $response = static::request('GET', null, $params);

        $rows = [];

        foreach ($response  as $line) {
            $rows[] = new static($line);
        }

        return Collection::make($rows);
    }

    /**
     * Create a new resouce
     *
     * @param  array|Arrayable  $params
     * @return static
     */
    public static function create($params)
    {
        $response = static::request('POST', null, $params);

        return new static($response);
    }

    /**
     * Fetch the resource specified in th id
     *
     * @param  int|string $id
     * @return static
     */
    public static function fetch($id)
    {
        $response = static::request('GET', $id);

        return new static($response);
    }

    /**
     * Save the current resource.
     *
     * @return bool
     */
    public function save()
    {
        if ($this->id === null) {
            $this->attributes = array_merge(
                $this->toArray(),
                static::request('POST', null, $this)
            );
        }
    }

    /**
     * Detroy de current resource
     *
     * @return bool
     */
    public function delete()
    {
        $response  = static::request('DELETE', $this->id);

        $this->attributes = [];
    }

    /**
     * Invoke the request of the current resource
     *
     * @param  string $method
     * @param  null|int|string $id
     * @param  array|Arrayable $params
     * @return array
     */
    private static function request($method, $id, $params = [])
    {

        static::checkPrivateMethodCalled();

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
     * Check if the method can response to resource
     * Yo need set static property $badMethods on the class resource
     *
     * @return void
     */
    protected static function checkPrivateMethodCalled()
    {
        if (static::propertyExists('privateMethods')) {
            $trace  = Arr::last(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3));
            $method = $trace['function'];
            if (in_array($method, static::$privateMethods)) {
                $class = static::class;
                throw new BadMethodCallException("Method $method does not exist on $class.");
            }
        }
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
}
