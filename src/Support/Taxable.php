<?php

namespace Alegra\Support;

trait Taxable
{
    /**
     * Adds the ability to simulate filters
     */
    use Filter\Emulated;

    protected static $casts = [
        'percentage' => 'float'
    ];

    /**
     * Create a new filter instance
     *
     * @return \Illuminate\Api\Resource\Filter
     */
    protected static function filterWith()
    {
        static $filter;

        return $filter ?: $filter = (new Filter)->fillable('type', 'string');
    }
}
