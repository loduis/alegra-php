<?php

namespace Alegra\Support\Filter;

use Alegra\Support\Filter as SupportFilter;

trait Emulated
{
    public static function all($params = [])
    {
        $items   = parent::all($params);
        $filters = static::filters($params);
        $items   = static::applyFilters($items, $filters);
        $total   = static::metadataFilter($items, $filters);
        $items   = static::limitFilter($items, $filters);
        $items   = static::orderFilter($items, $filters);
        $items   = $items->values();

        if ($total !== null) {
            $items->total = $total;
        }

        return $items;
    }

    protected static function applyFilters($items, $filters)
    {
        $except = [
            'limit',
            'start',
            'order_direction',
            'order_field',
            'metadata'
        ];

        foreach ($filters->except($except) as $searchKey => $searchValue) {
            $items = $items->filter(function ($value) use ($searchKey, $searchValue) {
                $like = false;
                if ($searchKey == 'query') {
                    $searchKey = 'name';
                    $like = true;
                }
                if (array_key_exists($searchKey, $value->getAttributes())) {
                    $value = $value[$searchKey];
                    if (is_scalar($value)) {
                        return $like ? stripos($value, $searchValue) !== false : $value == $searchValue;
                    }
                    return in_array($searchValue, (array) $value);
                }
            });
        }

        return $items;
    }

    protected static function limitFilter($items, $filters)
    {
        $limit   = (int) $filters->limit;
        if ($limit) {
            $start = (int) $filters->start;
            $items = $items->slice($start, $limit);
        }

        return $items;
    }

    protected static function metadataFilter($items, $filters)
    {
        if ($filters->metadata) {
            return count($items);
        }
    }

    protected static function orderFilter($items, $filters)
    {
        if ($filters->orderField) {
            $field     = $filters->orderField;
            $direction = $filters->orderDirection ?: SupportFilter::ORDER_ASC;
            $items     = $items->sortBy($field, SORT_REGULAR, $direction == SupportFilter::ORDER_DESC);
        }

        return $items;
    }
}
