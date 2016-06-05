<?php

namespace Alegra;

use Alegra\Http\Eloquent\Collection;

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
        foreach ($items as $item) {
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
        if (!$item instanceof Price) {
            $item = new Price($item);
        }

        $this->items[] = $item;

        return $this;
    }

}
