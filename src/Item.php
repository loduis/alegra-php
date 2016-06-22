<?php

namespace Alegra;

class Item extends Resource
{
    /**
     * Transform attributes when receive the resource
     *
     * @var array
     */
    protected static $transforms = [
        'category'  => Category::class,
        'price'     => Item\Price::class . '[]', // this is a collection of Price
        'tax'       => Tax::class . '[]', // this a collection of Tax
        'inventory' => Item\Inventory::class
    ];

    /**
     * Add ability for support metadata
     */
    use Support\Metadata;
}
