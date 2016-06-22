<?php

namespace Alegra;

use Alegra\Support\Address;
use Illuminate\Support\Collection;

class Contact extends Resource
{
    const TYPE_CLIENT = 'client';

    const TYPE_PROVIDER = 'provider';

    protected static $path = 'contacts';

    protected static $transforms = [
        'seller'  => Seller::class,
        'address' => Address::class,
        'term'    => Term::class,
        'type'    => Collection::class
    ];
}
