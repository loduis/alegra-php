<?php

namespace Alegra;

final class Payment extends Resource
{
    /**
     * Add ability for support metadata
     */
    use Support\Metadata;

    protected static $casts = [
        'bankAccount' => BankAccount::class,
    ];

    public function void()
    {
        return $this->selfPost('void');
    }

    public function open()
    {
        return $this->selfPost('open');
    }
}
