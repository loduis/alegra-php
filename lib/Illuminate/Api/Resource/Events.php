<?php

namespace Illuminate\Api\Resource;

use Psr\Http\Message\ResponseInterface;

trait Events
{
    protected static $listeners =[];

    protected static $events = [
        'GET'   => 'getting',
        'POST'  => 'created',
        'PUT'   => 'updated',
        'PATCH' => 'updated',
        'DELETE' => 'deleted'
    ];

    protected static $registredEvents = [];

    protected static function getEventName($method)
    {
        return static::$events[$method];
    }

    protected static function fireResourceEvent($method, $response)
    {
        $eventName = static::getEventName($method);

        static::registerResourceEvent($eventName);

        return static::fireEvent($eventName, $response);
    }

    protected static function addEvent($eventName, callable $listener)
    {
        $hash = static::hashEvent($eventName);

        static::$listeners[$hash][] = $listener;
    }

    protected static function fireEvent($eventName, ResponseInterface $response)
    {
        $hash = static::hashEvent($eventName);
        if (isset(static::$listeners[$hash])) {
            $listeners = (array) static::$listeners[$hash];
            foreach ($listeners as $listener) {
                $result = call_user_func($listener, $response);
                if ($result instanceof ResponseInterface) {
                    $response = $result;
                }
            }
        }

        return $response;
    }

    protected static function registerResourceEvent($eventName)
    {
        $hash = static::hashEvent($eventName);
        if (!isset(static::$registredEvents[$hash])) {
            $resourceName = static::class;
            $methodName = 'on' . ucfirst($eventName) . 'Resource';
            if (method_exists($resourceName, $methodName)) {
                static::$registredEvents[$hash] = true;
                static::addEvent($eventName, [$resourceName, $methodName]);
            }
        }
    }

    protected static function hashEvent($eventName = null)
    {
        return md5(static::class . $eventName);
    }
}
