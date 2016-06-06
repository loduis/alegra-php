<?php

namespace Alegra\Tests;

use Alegra\Api;
use Faker\Factory as Faker;
use Illuminate\Api\Testing\ApiHandler;
use Illuminate\Api\Testing\TestCase as ApiTestCase;

/**
 * Base class for Alegra test cases, provides some utility methods for creating
 * objects.
 */
abstract class TestCase extends ApiTestCase
{
    protected $faker;

    protected function setUp()
    {
        static $register;

        $apiUser = getenv('API_USER');
        $apiKey = getenv('API_KEY');
        Api::auth($apiUser, $apiKey);

        // If is live run over the alegra server
        $mode = getenv('API_ENV');
        if ($_SERVER['argc'] === 3) {
            $mode = $_SERVER['argv'][2];
        }
        if ($mode !== 'live') {
            $handler = new ApiHandler(__DIR__ . '/schemas');
            Api::createClient([
                'handler' => $handler
            ]);
        }

        $this->faker = Faker::create('es_ES');
    }
}
