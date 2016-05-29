<?php

namespace Alegra\Tests;

use Alegra\Api;
use Mockery as m;
use Faker\Factory as Faker;
use GuzzleHttp\ClientInterface;
use Alegra\Http\Client as HttpClient;
use Alegra\Tests\Http\Client as HttpClientMock;

/**
 * Base class for Alegra test cases, provides some utility methods for creating
 * objects.
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $faker;

    protected function setUp()
    {
        $apiUser = getenv('ALEGRA_API_USER');
        $apiKey = getenv('ALEGRA_API_KEY');
        Api::version(1);
        Api::auth($apiUser, $apiKey);

        $this->registerMockClient();

        $this->faker = Faker::create('es_ES');
    }

    public function tearDown()
    {
        m::close();
    }

    protected function registerMockClient()
    {
        $client   = m::mock(ClientInterface::class);
        $client->shouldReceive('request')
            ->andReturnUsing(function ($method, $path, $options) {
                return HttpClientMock::create(__DIR__ . '/schemas')
                    ->request($method, $path, $options);
            });

        HttpClient::set($client);
    }
 }
