<?php

namespace Alegra;

use Illuminate\Api\Http\Restable;
use Illuminate\Api\Http\Resource as ApiResource;

/**
 * Base resource
 */
abstract class Resource extends ApiResource
{
    use Restable;
    
    public static function first()
    {
        return static::all([
            'limit' => 1
        ])->first();
    }
}
