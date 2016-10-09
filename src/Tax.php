<?php

namespace Alegra;

class Tax extends Resource
{
    /**
     * Define collection of this model
     */
    const collection = self::class  . '[]';

    /**
     * Adds the ability to simulate filters
     */
    use Support\Filter\Emulated;

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

        return $filter ?: $filter = (new Support\Filter)->fillable('type', 'string');
    }
}
