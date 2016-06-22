<?php

namespace Alegra;

class Invoice extends Resource
{
    /**
     * Add ability for support of send email
     */
    use Support\Mailable;

    /**
     * Add ability for support metadata
     */
    use Support\Metadata;
}
