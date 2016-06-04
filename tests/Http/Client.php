<?php

namespace Alegra\Tests\Http;

use Alegra\Http\Client as HttpClient;
use Alegra\Tests\Http\Handler\JsonApiHandler;

class Client
{

    public static function register($filePath)
    {
        static $registed;

        if (!$registed) {
            $handler = new JsonApiHandler($filePath);
            HttpClient::create([
                'handler' => $handler
            ]);

            $registed = true;
        }
    }
}
