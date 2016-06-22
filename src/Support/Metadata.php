<?php

namespace Alegra\Support;

use Illuminate\Support\Arr;
use Illuminate\Api\Resource\Collection;

trait Metadata
{
    public static function all($params = [])
    {
        $metadata = [];
        $response = static::requestToArray('GET', null, $params);

        if (Arr::has($response, 'metadata')) {
            $metadata = Arr::pull($response, 'metadata');
            $response = Arr::pull($response, 'data');
        }

        // Create collection of current class

        $collection = Collection::makeOf(static::class, $response);

        // Set metada property to main object

        foreach ($metadata as $key => $value) {
            $collection->$key = $value;
        }

        return $collection;
    }
}
