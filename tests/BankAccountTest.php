<?php

namespace Alegra\Tests;

use Alegra\BankAccount;

class BankAccountTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals('bank-accounts', BankAccount::resolvePath());
    }
}
