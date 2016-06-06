<?php

namespace Alegra;

use BadMethodCallException;
use Illuminate\Api\Http\Api as HttpApi;

class Api
{
    /**
     * The current version of api
     *
     * @var int
     */
    const VERSION  = 1;

    /**
     * The base path of api
     *
     * @var string
     */
    const BASE_URI = 'https://app.alegra.com/api/';

    public static function auth(...$auth)
    {
        HttpApi::auth(...$auth);
        HttpApi::version(static::VERSION);
        HttpApi::baseUri(static::BASE_URI . 'v' . HttpApi::version() . '/');
        HttpApi::createClient();
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param  string $method
     * @param  array $params
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        if (!method_exists(HttpApi::class, $method)) {
            throw new BadMethodCallException("Method {$method} does not exist.");
        }

        return call_user_func_array([HttpApi::class, $method], $params);
    }
}
