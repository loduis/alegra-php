<?php

namespace Alegra;

final class Client extends Contact
{
    /**
     * Define a contact type client
     */
    const TYPE = self::TYPE_CLIENT;

    /**
     * Add support por current type client
     */
    use Contact\Typeable;
}
