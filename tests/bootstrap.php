<?php

namespace GuzzleHttp {

    /**
     * Wrapper for json_decode that throws when an error occurs.
     *
     * @param string $json    JSON data to parse
     * @param bool $assoc     When true, returned objects will be converted
     *                        into associative arrays.
     * @param int    $depth   User specified recursion depth.
     * @param int    $options Bitmask of JSON decode options.
     *
     * @return mixed
     * @throws \InvalidArgumentException if the JSON cannot be decoded.
     * @link http://www.php.net/manual/en/function.json-decode.php
     */
    //if (!function_exists('json_decode')) {
        function json_decode($json, $assoc = false, $depth = 512, $options = 0)
        {
            $data = \json_decode($json, $assoc, $depth, $options);
            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new \InvalidArgumentException(
                    'json_decode error: ' . json_last_error_msg());
            }

            return $data;
        }
    //}

    /**
     * Wrapper for JSON encoding that throws when an error occurs.
     *
     * @param string $value   The value being encoded
     * @param int    $options JSON encode option bitmask
     * @param int    $depth   Set the maximum depth. Must be greater than zero.
     *
     * @return string
     * @throws \InvalidArgumentException if the JSON cannot be encoded.
     * @link http://www.php.net/manual/en/function.json-encode.php
     */
    //if (!function_exists('json_encode')) {
        function json_encode($value, $options = 0, $depth = 512)
        {
            $json = \json_encode($value, $options, $depth);
            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new \InvalidArgumentException(
                    'json_encode error: ' . json_last_error_msg());
            }

            return $json;
        }
    //}
}

namespace {

    use Dotenv\Dotenv;
    use Alegra\Tests\Http\Client;

    set_error_handler(function ($errno, $errstr, $errfile, $errline) {
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    });

    /*
    |--------------------------------------------------------------------------
    | Register The Composer Auto Loader
    |--------------------------------------------------------------------------
    |
    | Composer provides a convenient, automatically generated class loader
    | for our application. We just need to utilize it! We'll require it
    | into the script here so that we do not have to worry about the
    | loading of any our classes "manually". Feels great to relax.
    |
    */
    require __DIR__ . '/../vendor/autoload.php';

    (new Dotenv(__DIR__))->load();
}
