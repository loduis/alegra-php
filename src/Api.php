<?php

namespace Alegra;

class Api
{
    /**
     * The current version of api
     */
    const VERSION  = 1;

    /**
     * The base path of api
     */
    const BASE_URI = 'https://app.alegra.com/api/';

    /**
     * The user for auth on api
     *
     * @var string
     */
    private static $user;

    /**
     * The api token for auth
     *
     * @var string
     */
    private static $token;

    /**
     * Custom version of api for future request
     *
     * @var int
     */
    private static $version = self::VERSION;

    /**
     * Set and get the current version of api
     *
     * @param  null|int $number
     * @return void|int
     */
    public static function version($number = null)
    {
        if ($number === null) {
            return static::$version;
        }

        static::$version = (int) $number;
    }

    /**
     * Get the base path of api
     *
     * @return string
     */
    public static function baseUri()
    {
        return static::BASE_URI . 'v' . static::$version . '/';
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
}
