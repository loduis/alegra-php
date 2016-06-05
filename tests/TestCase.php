<?php

namespace Alegra\Tests;

use Alegra\Api;
use Mockery as m;
use Faker\Factory as Faker;

/**
 * Base class for Alegra test cases, provides some utility methods for creating
 * objects.
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $faker;

    protected function setUp()
    {
        $apiUser = getenv('API_USER');
        $apiKey = getenv('API_KEY');
        Api::version(1);
        Api::auth($apiUser, $apiKey);

        // If is live run over the alegra server
        $mode = getenv('API_ENV');
        if ($_SERVER['argc'] === 3) {
            $mode = $_SERVER['argv'][2];
        }
        if ($mode !== 'live') {
            Http\Client::register(__DIR__ . '/schemas');
        }

        $this->faker = Faker::create('es_ES');
    }

    protected function assertPrivate($class, $method)
    {
        $this->assertTrue((new \ReflectionClass($class))->getMethod($method)->isPrivate());
    }

    protected function faker($property)
    {
        return $this->faker->$property;
    }
}
