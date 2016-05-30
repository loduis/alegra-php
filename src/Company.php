<?php

namespace Alegra;

use Alegra\Http\Resource;
use Alegra\Http\Restable;

class Company extends Resource
{
    use Restable {
        all as private;
        delete as private;
        fetch as fetchResource;
    }

    protected static $path = 'company';

    public function __construct($attributes = [])
    {
        if (!$attributes) {
            $attributes = static::request('GET');
        }

        parent::__construct($attributes);
    }

    public static function fetch()
    {
        return static::fetchResource(null);
    }
}
