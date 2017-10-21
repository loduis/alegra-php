<?php

namespace Alegra;

final class Warehouse extends Resource
{
    /**
     * Define collection of this model
     */
    const collection = self::class  . '[]';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected static $casts = [
        'initialQuantity' => 'float',
        'availableQuantity' => 'float'
    ];
}
