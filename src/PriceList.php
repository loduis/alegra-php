<?php

namespace Alegra;

use Illuminate\Api\Resource\Collection;

class PriceList extends Collection
{
    /**
     * Create a new collection.
     *
     * @param  mixed  $items
     * @return void
     */
    public function __construct($items = [])
    {
        foreach ((array) $items as $item) {
            $this->add($item);
        }
    }

    /**
     * Add an item to the collection.
     *
     * @param  mixed  $item
     * @return $this
     */
    public function add($item)
    {
        if (is_numeric($item)) {
            $item = [
                'id' => 1,
                'price' => $item
            ];
        }
        if (!$item instanceof Price) {
            $item = new Price($item);
        }

        $this->items[] = $item;

        return $this;
    }
}
