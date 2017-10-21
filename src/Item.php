<?php

namespace Alegra;

use Alegra\Support\Attachment;

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
        'inventory'   => Item\Inventory::class,
        'images'      => Attachment::collection,
        'attachments' => Attachment::collection
    ];

    /**
     * Add ability for support metadata
     */
    use Support\Metadata;

    /**
     * Add ability for support attach file and images
     */
    use Support\Photoable;
}
