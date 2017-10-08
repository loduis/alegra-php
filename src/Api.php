<?php

namespace Alegra;

use Illuminate\Support\Arr;
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
        HttpApi::baseUri(static::BASE_URI . static::VERSION . '/');
        $options = static::$clientOptions;
        $options['headers']['User-Agent'] = 'Alegra/' . static::VERSION .
            ' PhpBindings/' . static::bindingVersion();
        if (count($auth) === 1 && Arr::isAssoc($auth[0])) {
            $params = $auth[0];
            if (Arr::has($params, 'email') && Arr::has($params, 'password')) {
                $client = HttpApi::createClient($options);
                $response = $client::toArray('post', 'login', $params);
                $auth = [
                    [
                        $params['email'],
                        $response['token']
                    ]
                ];
            }
        }

        HttpApi::auth(...$auth);
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

    protected static function bindingVersion()
    {
        $content = file_get_contents(__DIR__ . '/../package.json');
        $options = json_decode($content);

        return $options->version;
    }
}
