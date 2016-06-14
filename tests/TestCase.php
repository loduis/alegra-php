<?php

namespace Alegra\Tests;

use Alegra\Api;
use Faker\Factory as Faker;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Api\Testing\ApiHandler;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
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
        static $handler;
        // If is live run over the alegra server
        $mode = getenv('API_ENV');

        if ($_SERVER['argc'] === 3) {
            $mode = $_SERVER['argv'][2];
        }

        if ($mode !== 'live') {
            if (!$handler) {
                $handler = ApiHandler::create(__DIR__ . '/schemas')
                    ->request('POST /items', function (RequestInterface $request, $options) {
                        $price = array_get($options['json'], 'price');
                        $name = array_get($options['json'], 'name');
                        if (is_null($price) || is_numeric($name)) {
                            return $this->createResponse(400, [

                            ]);
                        }
                    })
                    ->request('GET /taxes', function (RequestInterface $request) {
                        return $this->createResponse(200, [
                              [
                                'id' => 1,
                                'name' => 'IVA',
                                'percentage' => '5.00',
                                'description' => '',
                                'type' => 'IVA',
                              ],
                              [
                                'id' => 2,
                                'name' => 'IVA',
                                'percentage' => '5.00',
                                'description' => '',
                                'type' => 'IVA',
                              ],
                        ]);
                    });
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
