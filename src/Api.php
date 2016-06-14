<?php

namespace Alegra;

use BadMethodCallException;
use Illuminate\Api\Http\Api as HttpApi;

class Api
{
    /**
     * The current version of package
     *
     * @var int
     */
    const VERSION  = 1;

    /**
     * The current version of php bindings
     *
     * @var  string
     */
    const BINDING_VERSION = '0.13.2';

    /**
     * Custom options of http client
     *
     * @var array
     */
    private static $clientOptions = [];

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
        HttpApi::baseUri(static::BASE_URI . HttpApi::version() . '/');
        $options = static::$clientOptions;
        $options['headers']['User-Agent'] = 'Alegra/' . HttpApi::version() .
                                            ' PhpBindings/' . static::BINDING_VERSION;
        HttpApi::createClient($options);
    }

    /**
     * Set the custom options for create http client
     *
     * @param  array  $options
     * @return void
     */
    public static function clientOptions(array $options)
    {
        static::$clientOptions = $options;
    }
}
