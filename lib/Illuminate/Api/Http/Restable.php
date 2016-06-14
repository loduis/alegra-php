<?php

namespace Illuminate\Api\Http;

use UnexpectedValueException;
use Illuminate\Api\Resource\Collection;

/**
 * Add ability of rest resurce
 */
trait Restable
{
    /**
     * Fetch all resources
     *
     * @param  array|\Illuminate\Contracts\Support\Arrayable $params
     * @return array
     */
    public static function all($params = [])
    {
        return Collection::make(static::instanceFromRequest('GET', null, $params));
    }

    /**
     * Create a new resouce
     *
     * @param  array|\Illuminate\Contracts\Support\Arrayable $params
     * @return static
     */
    public static function create($params)
    {
        return static::instanceFromRequest('POST', null, new static($params));
    }

    /**
     * Get the resource by id
     *
     * @param  int|string $id
     * @return static
     */
    public static function get($id)
    {
        if (!$id) {
            throw new UnexpectedValueException('The id parameter is required.');
        }

        return static::instanceFromRequest('GET', $id);
    }

    /**
     * Save the current resource.
     *
     * @return $this
     */
    public function save()
    {
        $id = $this->id;
        $method = $id === null ? 'POST' : 'PUT';

        return $this->store($method, $id);
    }

    /**
     * Delete method with support for instance a class methods
     *
     * @param  mixed $resource
     * @return $this
     */
    protected static function macroDeleteHandler($resource)
    {
        $id = $resource;
        if ($isResource = static::isResource($resource)) {
            $id = $resource->id;
        }

        if (!$id) {
            throw new UnexpectedValueException('The id attribute is required.');
        }

        $attributes = static::requestToArray('DELETE', $id);

        return $isResource ? $resource->fill($attributes) : new static($attributes);
    }
}
