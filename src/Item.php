<?php

namespace Alegra;

final class Item extends Resource
{
    /**
     * Transform attributes when receive the resource
     *
     * @var array
     */
    protected $fillable = [
        'name'        => 'string',
        'type'        => 'string',
        'description' => 'string',
        'reference'   => 'string',
        'status'      => 'string',
        'category'    => Category::class,
        'price'       => Item\Price::collection, // this is a collection of Price
        'tax'         => Tax::collection, // this a collection of Tax
        'inventory'   => Item\Inventory::class
    ];

    /**
     * Add ability for support metadata
     */
    use Support\Metadata;
}
