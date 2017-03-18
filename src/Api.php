<?php

namespace Alegra;

use Illuminate\Api\Http\Api as HttpApi;

class Api
{
    /**
     * The current version of package
     *
     * @var int
     */
    const VERSION  = 'v1';

    /**
     * The current version of php bindings
     *
     * @var  string
     */
    const BINDING_VERSION = '0.21.0';

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
        HttpApi::baseUri(static::BASE_URI . static::VERSION . '/');
        $options = static::$clientOptions;
        $options['headers']['User-Agent'] = 'Alegra/' . static::VERSION .
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
