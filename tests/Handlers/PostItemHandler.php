<?php

namespace Alegra\Tests\Handlers;

use Psr\Http\Message\RequestInterface;

class PostItemHandler
{
    public function __invoke(RequestInterface $request, array $options)
    {
        $price = array_get($options['json'], 'price');
        $name = array_get($options['json'], 'name');
        if (is_null($price) || is_numeric($name)) {
            return 400; // bad request status code
        }
    }
}
