<?php

namespace Alegra;

use Illuminate\Api\Http\Resource as ApiResource;

/**
 * Base resource
 */
abstract class Resource extends ApiResource
{
    public static function first()
    {
        return static::all([
            'limit' => 1
        ])->first();
    }
}
