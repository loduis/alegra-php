<?php

namespace Alegra;

use Illuminate\Api\Http\Resource;
use Illuminate\Api\Http\Restable;

class Category extends Resource
{
    protected static $casts = [
        'id' => 'int'
    ];

    use Restable;
}
