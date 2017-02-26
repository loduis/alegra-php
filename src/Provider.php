<?php

namespace Alegra;

final class Provider extends Contact
{
    /**
     * Define a contact type provider
     */
    const TYPE = self::TYPE_PROVIDER;

    /**
     * Add support por current type provider
     */
    use Contact\Typeable;
}
