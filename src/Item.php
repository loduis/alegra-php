<?php

namespace Alegra;

use Illuminate\Api\Http\Resource;
use Illuminate\Api\Http\Restable;

class Item extends Resource
{
    /**
     * Transform attributes when receive the resource
     *
     * @var [type]
     */
    protected static $transforms = [
        'category' => Category::class,
        'price' => PriceList::class
    ];

    use Restable;

    public function setPriceAttribute($value)
    {
        if (!$value instanceof PriceList) {
            $value = PriceList::make([$value]);
        }

        $this->attributes['price'] = $value;
    }
}
