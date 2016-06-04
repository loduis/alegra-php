<?php

namespace Alegra\Http;

use UnexpectedValueException;
use Illuminate\Support\Collection;

trait Restable
{
    /**
     * Fetch all resources
     *
     * @param  array|Arrayable $params
     * @return array
     */
    public static function all($params = [])
    {
        return Collection::make(static::createFromRequest('GET', null, $params));
    }

    /**
     * Create a new resouce
     *
     * @param  array|Arrayable  $params
     * @return static
     */
    public static function create($params)
    {
        return static::createFromRequest('POST', null, $params);
    }

    /**
     * Fetch the resource by id
     *
     * @param  int|string $id
     * @return static
     */
    public static function fetch($id)
    {
        if (!$id) {
            throw new UnexpectedValueException('The id parameter is required.');
        }

        return static::createFromRequest('GET', $id);
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
     * Detroy de current resource
     *
     * @return $this
     */
    public function delete()
    {
        if (!$this->id) {
            throw new UnexpectedValueException('The id attribute is required.');
        }

        static::request('DELETE', $this->id);

        $this->attributes = [];

        return $this;
    }
}
