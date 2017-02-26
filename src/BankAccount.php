<?php

namespace Alegra;

final class BankAccount extends Resource
{
    /**
     * Adds the ability to simulate filters
     */
    use Support\Filter\Emulated;

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
