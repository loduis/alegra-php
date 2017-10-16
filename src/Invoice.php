<?php

namespace Alegra;

final class Invoice extends Resource
{
    protected static $casts = [
        'attachments'      => Support\Attachment::collection
    ];
    /**
     * Add ability for support of send email
     */
    use Support\Mailable;

    /**
     * Add ability for support metadata
     */
    use Support\Metadata;

    /**
     * Add ability for support attach file
     */
    use Support\Attachable;
}
