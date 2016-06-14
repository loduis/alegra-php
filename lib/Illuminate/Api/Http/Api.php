<?php

namespace Illuminate\Api\Http;

abstract class Api
{
    /**
     * The user for auth on api
     *
     * @var string
     */
    protected static $user;

    /**
     * The api token for auth
     *
     * @var string
     */
    protected static $token;

    /**
     * Custom version of api for future request
     *
     * @var int
     */
    protected static $version;

    /**
     * The base uri for request
     *
     * @var string
     */
    protected static $uri;

    /**
     * Set and get the current version of api
     *
     * @param  null|int $number
     * @return void|int
     */
    public static function version($number = null)
    {
        if ($number === null) {
            return 'v' . static::$version;
        }

        static::$version = (int) $number;
    }

    /**
     * Get the base path of api
     *
     * @return string
     */
    public static function baseUri($uri = null)
    {
        if ($uri === null) {
            return static::$uri;
        }

        static::$uri = $uri;
    }

    /**
     * Set and get the auth
     * @param string|array $auth
     * @return void|array
     */
    public static function auth(...$auth)
    {
        $count = count($auth);
        if ($count === 0) {
            if (static::$user === null) {
                return static::$token;
            }
            return [
                static::$user,
                static::$token
            ];
        } elseif ($count === 1) {
            list(static::$token) = $auth;
        } else {
            list(static::$user, static::$token) = $auth;
        }
    }

    /**
     * Create http client for request handle
     *
     * @param  array  $options
     * @return void
     */
    public static function createClient(array $options = [])
    {
        Client::create(array_merge(
            $options,
            [
                'base_uri' => static::baseUri(),
                'auth'     => static::auth(),
            ]
        ));
    }
}
