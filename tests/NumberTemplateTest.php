<?php

namespace Alegra\Tests;

use Alegra\NumberTemplate;

class NumberTemplateTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals('number-templates', NumberTemplate::resolvePath());
    }
}
