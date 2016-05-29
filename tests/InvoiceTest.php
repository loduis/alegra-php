<?php

namespace Alegra\Tests;

use Alegra\Invoice;

class InvoiceTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals('invoices', Invoice::resolvePath());
    }
}
