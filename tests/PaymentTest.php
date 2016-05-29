<?php

namespace Alegra\Tests;

use Alegra\Payment;

class PaymentTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals('payments', Payment::resolvePath());
    }
}
