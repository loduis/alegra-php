<?php

namespace Alegra;

class Customer extends Contact
{
    const TYPE = self::TYPE_CUSTOMER;

    use Contact\Typeable;
}
