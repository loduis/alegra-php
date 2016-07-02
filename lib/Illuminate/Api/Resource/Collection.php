<?php

namespace Illuminate\Api\Resource;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection as BaseCollection;

class Collection extends BaseCollection
{

    protected $itemClass;

    /**
     * Create a new collection instance of.
     *
     * @param  mixed  $items
     * @return static
     */
    public static function makeOf($className, $items)
    {
        $collection = (new static)->itemClass($className);
        if ($items instanceof $className) {
            $items = [
                $items
            ];
        }
        foreach ((array) $items as $item) {
            $collection->push(static::getOfClass($className, $item));
        }

        return $collection;
    }

    public function itemClass($className)
    {
        $this->itemClass = $className;

        return $this;
    }

    /**
     * Find a model in the collection by key.
     *
     * @param  mixed  $key
     * @param  mixed  $default
     * @return \Illuminate\Api\Resource\Model
     */
    public function find($key, $default = null)
    {
        if ($key instanceof Model) {
            $key = $key->getKey();
        }

        return Arr::first($this->items, function ($model) use ($key) {
            return $model->getKey() == $key;
        }, $default);
    }

    /**
     * Add an item to the collection.
     *
     * @param  mixed  $item
     * @return $this
     */
    public function add($item)
    {
        $this->items[] = static::getOfClass($this->itemClass, $item);

        return $this;
    }

    protected static function getOfClass($className, $item)
    {
        if ($className && !($item instanceof $className)) {
            $item = new $className($item);
        }

        return $item;
    }

    /**
     * Determine if a key exists in the collection.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return bool
     */
    public function contains($key, $value = null)
    {
        if (func_num_args() == 2) {
            return parent::contains($key, $value);
        }

        if ($this->useAsCallable($key)) {
            return parent::contains($key);
        }

        $key = $key instanceof Model ? $key->getKey() : $key;

        return parent::contains(function ($model) use ($key) {
            return $model->getKey() == $key;
        });
    }

    /**
     * Get the array of primary keys.
     *
     * @return array
     */
    public function modelKeys()
    {
        return array_map(function ($model) {
            return $model->getKey();
        }, $this->items);
    }

    /**
     * Merge the collection with the given items.
     *
     * @param  \ArrayAccess|array  $items
     * @return static
     */
    public function merge($items)
    {
        $dictionary = $this->getDictionary();

        foreach ($items as $item) {
            $dictionary[$item->getKey()] = $item;
        }

        return new static(array_values($dictionary));
    }

    /**
     * Diff the collection with the given items.
     *
     * @param  \ArrayAccess|array  $items
     * @return static
     */
    public function diff($items)
    {
        $diff = new static;

        $dictionary = $this->getDictionary($items);

        foreach ($this->items as $item) {
            if (! isset($dictionary[$item->getKey()])) {
                $diff->add($item);
            }
        }

        return $diff;
    }

    /**
     * Intersect the collection with the given items.
     *
     * @param  \ArrayAccess|array  $items
     * @return static
     */
    public function intersect($items)
    {
        $intersect = new static;

        $dictionary = $this->getDictionary($items);

        foreach ($this->items as $item) {
            if (isset($dictionary[$item->getKey()])) {
                $intersect->add($item);
            }
        }

        return $intersect;
    }

    /**
     * Return only unique items from the collection.
     *
     * @param  string|callable|null  $key
     * @return static
     */
    public function unique($key = null)
    {
        if (! is_null($key)) {
            return parent::unique($key);
        }

        return new static(array_values($this->getDictionary()));
    }

    /**
     * Returns only the models from the collection with the specified keys.
     *
     * @param  mixed  $keys
     * @return static
     */
    public function only($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        $dictionary = Arr::only($this->getDictionary(), $keys);

        return new static(array_values($dictionary));
    }

    /**
     * Returns all models in the collection except the models with specified keys.
     *
     * @param  mixed  $keys
     * @return static
     */
    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        $dictionary = Arr::except($this->getDictionary(), $keys);

        return new static(array_values($dictionary));
    }

    /**
     * Get a dictionary keyed by primary keys.
     *
     * @param  \ArrayAccess|array|null  $items
     * @return array
     */
    public function getDictionary($items = null)
    {
        $items = is_null($items) ? $this->items : $items;

        $dictionary = [];

        foreach ($items as $value) {
            $dictionary[$value->getKey()] = $value;
        }

        return $dictionary;
    }

    /**
     * The following methods are intercepted to always return base collections.
     */

    /**
     * Get an array with the values of a given key.
     *
     * @param  string  $value
     * @param  string|null  $key
     * @return \Illuminate\Support\Collection
     */
    public function pluck($value, $key = null)
    {
        return $this->toBase()->pluck($value, $key);
    }

    /**
     * Get the keys of the collection items.
     *
     * @return \Illuminate\Support\Collection
     */
    public function keys()
    {
        return $this->toBase()->keys();
    }

    /**
     * Zip the collection together with one or more arrays.
     *
     * @param  mixed $items
     * @return \Illuminate\Support\Collection
     */
    public function zip($items)
    {
        $items = is_array($items) ? $items : func_get_args();

        return call_user_func_array([$this->toBase(), 'zip'], $items);
    }

    /**
     * Collapse the collection of items into a single array.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collapse()
    {
        return $this->toBase()->collapse();
    }

    /**
     * Get a flattened array of the items in the collection.
     *
     * @param  int  $depth
     * @return \Illuminate\Support\Collection
     */
    public function flatten($depth = INF)
    {
        return $this->toBase()->flatten($depth);
    }

    /**
     * Flip the items in the collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function flip()
    {
        return $this->toBase()->flip();
    }

    /**
     * Get a base Support collection instance from this collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function toBase()
    {
        return new BaseCollection($this->items);
    }

    /**
     * Get all of the items in the collection.
     *
     * @return array
     */
    public function allVisible()
    {
        $items = [];

        foreach ($this->items as $item) {
            if ($item instanceof Model) {
                $items[] = $item->toArrayVisible();
            } else {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * Attribute for debug info
     *
     * @return array
     */
    public function __debugInfo()
    {
        return $this->all();
    }
}
