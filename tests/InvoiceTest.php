<?php

namespace Alegra\Tests;

use Alegra\Invoice;

class InvoiceTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals('invoices', Invoice::resolvePath());
    }

    public function testSend()
    {
        //(new Invoice(86702))->send('loduis@gmail.com', 'loduis@myabakus.com');
        Invoice::mail(86702)
            ->to('loduis@gmail.com')
            ->copyMe()
            ->sendAsCopy();
    }
}
