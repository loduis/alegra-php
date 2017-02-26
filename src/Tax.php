<?php

namespace Alegra;

final class Tax extends Resource
{
    /**
     * Define collection of this model
     */
    const collection = self::class  . '[]';

    /**
     * Add ability for support taxable type
     */
    use Support\Taxable;
}
