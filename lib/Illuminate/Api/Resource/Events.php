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

    /**
     * Register a new event
     *
     * @param string   $eventName
     * @param callable $listener
     */
    protected static function registerEvent($eventName, callable $listener)
    {
        $hash = static::hashEvent($eventName);

        static::$listeners[$hash][] = $listener;
    }

    /**
     * Fire all events an return the new response
     *
     * @param  string            $eventName
     * @param  ResponseInterface $response
     * @return ResponseInterface
     */
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

    /**
     * Registre any event defined in the class, supported static method like:
     *
     * onCreatedResource(ResponseInterface $response)
     * onUpdateResource(ResponseInterface $response)
     * onCreatedResource(ResponseInterface $response)
     * onDeletedResource(ResponseInterface $response)
     *
     * @param  string $eventName
     * @return void
     */
    protected static function registerResourceEvent($eventName)
    {
        $hash = static::hashEvent($eventName);
        if (!isset(static::$registredEvents[$hash])) {
            $resourceName = static::class;
            $methodName = 'on' . ucfirst($eventName) . 'Resource';
            if (method_exists($resourceName, $methodName)) {
                static::registerEvent($eventName, [$resourceName, $methodName]);
            }
            static::$registredEvents[$hash] = true;
        }
    }

    /**
     * Hash an event name
     *
     * @param  string $eventName
     * @return string
     */
    protected static function hashEvent($eventName = null)
    {
        return md5(static::class . $eventName);
    }
}
