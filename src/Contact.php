<?php

namespace Alegra;

use Alegra\Support\Address;
use Illuminate\Support\Collection;

class Contact extends Resource
{
    /**
     * Contact type client
     */
    const TYPE_CLIENT = 'client';

    /**
     * Contact type provider
     */
    const TYPE_PROVIDER = 'provider';

    /**
     * Resource path
     *
     * @var string
     */
    protected static $path = 'contacts';

    /**
     * Property transforms
     *
     * @var array
     */
    protected static $casts = [

        'seller'  => Seller::class,
        'address' => Address::class,
        'term'    => Term::class,
        'type'    => Collection::class
    ];

    /**
     * Add ability for support metadata
     */
    use Support\Metadata;

    protected static function filterWith()
    {
        return (new Support\Filter)->fillable('type', 'string');
    }
}
