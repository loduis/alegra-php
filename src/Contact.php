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
    protected $fillable = [
        'name'             => 'string',
        'identification'   => 'string',
        'email'            => 'string',
        'phonePrimary'     => 'string',
        'phoneSecondary'   => 'string',
        'fax'              => 'string',
        'mobile'           => 'string',
        'observations'     => 'string',
        'address'          => Address::class,
        'type'             => Collection::class,
        'seller'           => Seller::class,
        'term'             => Term::class,
        'priceList'        => PriceList::class,
        'internalContacts' => Contact\Internal::collection
    ];

    /**
     * Add ability for support metadata
     */
    use Support\Metadata;

    protected static function filterWith()
    {
        static $filter;

        return $filter ?: $filter = (new Support\Filter)->fillable('type', 'string');
    }
}
