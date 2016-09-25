<?php

namespace Illuminate\Api\Testing;

use PHPUnit\Framework\TestCase as BaseTest;

/**
 * Base class for Alegra test cases, provides some utility methods for creating
 * objects.
 */
abstract class TestCase extends BaseTest
{
    protected function assertPrivate($class, $method)
    {
        $this->assertTrue((new \ReflectionClass($class))->getMethod($method)->isPrivate());
    }

    protected function faker($property)
    {
        return $this->faker->$property;
    }
}
