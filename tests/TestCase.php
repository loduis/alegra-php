<?php

namespace Alegra\Tests;

use Alegra\Api;
use Faker\Factory as Faker;
use GuzzleHttp\HandlerStack;
use Illuminate\Api\Testing\ApiHandler;
use Illuminate\Api\Testing\TestCase as ApiTestCase;

/**
 * Base class for Alegra test cases, provides some utility methods for creating
 * objects.
 */
abstract class TestCase extends ApiTestCase
{
    /**
     * Faker quick helper instance
     *
     * @var [type]
     */
    protected $faker;

    protected function setUp()
    {
        static $handler;
        // If is live run over the alegra server
        $mode = getenv('API_ENV');

        if ($_SERVER['argc'] >= 3) {
            $mode = $_SERVER['argv'][2];
        }

        if ($mode !== 'live') {
            if (!$handler) {
                $handler = ApiHandler::create(__DIR__ . '/schemas')
                    ->request('POST /contacts', Handlers\PostContactHandler::class)
                    ->request('POST /items', Handlers\PostItemHandler::class);
            }
            $stack = HandlerStack::create($handler);
            Api::clientOptions([
                'handler' => $stack
            ]);
        }

        $apiUser = getenv('API_USER');
        $apiKey = getenv('API_KEY');
        Api::auth($apiUser, $apiKey);

        $this->faker = Faker::create('es_ES');
    }
}
