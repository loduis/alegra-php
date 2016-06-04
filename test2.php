<?php

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client as HttpClient;
use Alegra\Tests\Http\Handler\JsonApiHandler;
use Alegra\Tests\Http\Middleware;

use Illuminate\Support\Arr;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

require './vendor/autoload.php';


$handler = new JsonApiHandler(__DIR__ . '/tests/schemas');
$stack = HandlerStack::create($handler);

/*
$stack->push(Middleware::mapResponse(function ($response, $request) {
    return $response->withHeader('X-Foo', 'bar');
}));*/

$client = new HttpClient(array_merge(
    [
        'base_uri' => 'https://app.alegra.com/api/v1/',
        'headers' => [
            'Accept' => 'application/json'
        ],
        'auth' => [
            'loduis@gmail.com',
            'c657c81a66c8c0a444d0'
        ]
    ],
    [
        'handler' => $stack
    ]
));

$handler->request('PUT /company', function (ResponseInterface $response, $request, $originalResource) {
    $requestBody = json_decode((string) $request->getBody(), true);
    if (Arr::has($requestBody, 'identification') &&
        Arr::has($originalResource, 'identification') &&
        (string) $requestBody['identification'] == (string) $originalResource['identification']

    ) {

    }

    return $response;
});


//$response = $client->post('invoices/10/email', ['json' => ['a' => 1, 'b' => '2']]);

$response = $client->put('company', [
    'json' => [
        'name' => 'Prueba'
    ]
]);

echo $response->getBody(), PHP_EOL;
//print_r($response->getHeader('content-type'));
