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
        'category' => Category::class,
        'price' => PriceList::class,
        'tax' => 'Alegra\Tax[]' // this an collection of Tax
    ];

    use Restable;
}
