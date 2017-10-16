<?php

namespace Alegra;

final class Payment extends Resource
{
    protected static $casts = [
        'bankAccount' => BankAccount::class,
        'attachments' => Support\Attachment::collection
    ];

    /**
     * Add ability for support metadata
     */
    use Support\Metadata;

    /**
     * Add ability for support attach file
     */
    use Support\Attachable;

    public function void()
    {
        return $this->postTo('void');
    }

    public function open()
    {
        return $this->postTo('open');
    }
}
