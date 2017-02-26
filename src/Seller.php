<?php

namespace Alegra;

final class Seller extends Resource
{
    const STATUS_ACTIVE = 'active';

    const STATUS_INACTIVE = 'inactive';

    /**
     * Adds the ability to simulate filters
     */
    use Support\Filter\Emulated;
}
