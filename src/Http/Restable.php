<?php

namespace Alegra\Http;

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
        $rows = static::instanceFromRequest('GET', null, $params);

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
        return static::instanceFromRequest('POST', null, $params);
    }

    /**
     * Fetch the resource specified in th id
     *
     * @param  int|string $id
     * @return static
     */
    public static function fetch($id)
    {
        return static::instanceFromRequest('GET', $id);
    }

    /**
     * Save the current resource.
     *
     * @return $this
     */
    public function save()
    {
        $method = $this->id === null ? 'POST' : 'PUT';
        $attributes = static::request($method, $this->id, $this);

        return $this->combine($attributes);
    }

    /**
     * Detroy de current resource
     *
     * @return $this
     */
    public function delete()
    {
        $response  = static::request('DELETE', $this->id);

        $this->attributes = [];

        return $this;
    }
}
