<?php

namespace Alegra;

final class Category extends Resource
{
    const collection = self::class  . '[]';

    const TYPE_INCOME    = 'income';

    const TYPE_EXPENSE   = 'expense';

    const TYPE_EQUITY    = 'equity';

    const TYPE_ASSET     = 'asset';

    const TYPE_LIABILITY = 'liability';

    /**
     * Adds the ability to simulate filters
     */
    use Support\Filter\Emulated;

    protected static $casts = [
        'children' => self::collection,
        'readOnly' => 'bool'
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
