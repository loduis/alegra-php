<?php

namespace Alegra\Support\Filter\Emulated;

use Alegra\Support\Filter as SupportFilter;
use Alegra\Support\Filter\Emulated as EmulatedFilter;

trait Type
{
    use EmulatedFilter;

    /**
     * Create a new filter instance
     *
     * @return \Illuminate\Api\Resource\Filter
     */
    protected static function filterWith()
    {
        static $filter;

        return $filter ?: $filter = (new SupportFilter)->fillable('type', 'string');
    }
}
