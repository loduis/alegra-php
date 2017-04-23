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
        $this->store('POST', $this->id . '/void');
    }

    public function open()
    {
        $this->store('POST', $this->id . '/open');
    }
}
