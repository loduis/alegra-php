<?php

namespace Alegra;

use Illuminate\Api\Http\Restable;

class Item extends Resource
{
    /**
     * Transform attributes when receive the resource
     *
     * @var array
     */
    protected static $transforms = [
        'category'  => Category::class,
        'price'     => Item\Price::class . '[]',
        'tax'       => Tax::class . '[]', // this an collection of Tax
        'inventory' => Item\Inventory::class
    ];

    use Restable;
}
