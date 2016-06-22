<?php

namespace Alegra;

class Client extends Contact
{
    const TYPE = self::TYPE_CLIENT;

    use Contact\Typeable;
}
