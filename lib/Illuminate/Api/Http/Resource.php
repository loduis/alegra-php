<?php

namespace Illuminate\Api\Http;

use BadMethodCallException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Api\Resource\Model;

abstract class Resource extends Model
{
    /**
     * Create a new resource  instance.
     *
     * @param  mixed    $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        if (is_scalar($attributes)) {
            $attributes = [
                $this->getKeyName() => $attributes
            ];
        }

        parent::__construct($attributes);
    }

    /**
     * Create instance from reques
     *
     * @param  string $method
     * @param  null|int|string $id
     * @param  array|\Illuminate\Contracts\Support\Arrayable $params
     * @return $this|array
     */
    protected static function instanceFromRequest($method, $id = null, $params = [])
    {
        $response = static::requestToArray($method, $id, $params);

        if (Arr::isAssoc($response)) {
            return new static($response);
        }

        $rows = [];

        foreach ($response as $line) {
            $rows[] = new static($line);
        }

        return $rows;
    }

    /**
     * Store and refresh object
     *
     * @param  string $method
     * @param  int|string $id
     * @return $this
     */
    protected function store($method, $id = null)
    {
        $attributes = static::requestToArray($method, $id, $this);

        return $this->fill($attributes);
    }

    /**
     * Invoke the request of the current resource
     *
     * @param  string $method
     * @param  null|int|string $id
     * @param  array|\Illuminate\Contracts\Support\Arrayable $params
     * @return array
     */
    protected static function request($method, $id = null, $params = [])
    {
        $path = static::resolvePath()  . ($id ? "/$id" : '');

        return Client::request($method, $path, $params);
    }

    /**
     * Request to json object
     *
     * @param  string $method
     * @param  null|int|string $id
     * @param  array|\Illuminate\Contracts\Support\Arrayable $params
     * @return array
     */
    protected static function requestToArray($method, $id = null, $params = [])
    {
        $response = static::request($method, $id, $params);

        return (array) json_decode($response->getBody(), true);
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

        if ($path = static::getStaticProperty('path')) {
            return $resolved[$class] = $path;
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
     * Check if resouce is an instance
     *
     * @param  mixed  $resource
     * @return bool
     */
    protected static function isResource($resource)
    {
        return $resource instanceof static;
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param  string $method
     * @param  array $params
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        return static::callMethod(static::class, $method, $params);
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param  string $method
     * @param  array $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        array_unshift($params, $this);

        return static::callMethod($this, $method, $params);
    }

    /**
     * Call a raw method
     *
     * @param  static|$this $objectOrClass
     * @param  string $method
     * @param  array $params
     * @return mixed
     */
    private static function callMethod($objectOrClass, $method, $params)
    {
        $method = 'macro' . ucfirst($method) . 'Handler';

        if (!method_exists($objectOrClass, $method)) {
            throw new BadMethodCallException("Method {$method} does not exist.");
        }

        return call_user_func_array([$objectOrClass, $method], $params);
    }
}
