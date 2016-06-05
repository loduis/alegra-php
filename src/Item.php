<?php

namespace Alegra;

use Alegra\Http\Resource;
use Alegra\Http\Restable;

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
        if (is_scalar($value)) {
            $value = PriceList::make([
                [
                    'id' => 1,
                    'price' => $value
                ]
            ]);
        } elseif ($value instanceof Price) {
            $value = PriceList::make([$value]);
        }

        $this->attributes['price'] = $value;
    }
}
