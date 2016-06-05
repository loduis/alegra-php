<?php

namespace Alegra\Http;

use BadMethodCallException;
use Illuminate\Support\Arr;
use Alegra\Http\Eloquent\Mail;

/**
 * adds the ability to appeal to be sent by email
 *
 * @method bool send(array $options)
 * @method bool send(...$emails)
 * @method bool static send($resource, array $options)
 * @method bool static send($resource, ...$emails)
 *
 * @method Mail mail()
 * @method Mail mail($resource)
 */
trait Mailable
{
    /**
     * Send email the raw method
     *
     * @param  int|static $resource
     * @param  array $options
     * @return bool
     */
    protected static function sendRaw($resource, ...$options)
    {
        if (static::isResource($resource)) {
            $resource = $resource->id;
        }

        if ($isAssoc = (count($options) == 1 && Arr::isAssoc($options[0]))) {
            $options = $options[0];
        }

        if (!$isAssoc) {
            $options = [
                'emails' => $options
            ];
        }

        $response = static::request('POST', $resource . '/email', $options);

        return $response->getStatusCode() === 200;
    }

    /**
     * Friendly method to send email
     *
     * @param  mixed $resource
     * @return Mail
     */
    protected static function mailRaw($resource)
    {
        if (!static::isResource($resource)) {
            $resource = new static($resource);
        }

        return new Mail($resource);
    }

    /**
     * Check if resouce is an instance
     *
     * @param  mixed  $resource
     * @return bool
     */
    protected static function isResource($resource)
    {
        return $resource instanceof static;
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
        return static::callMethod(static::class, $method, $params);
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param  string $method
     * @param  array $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        array_unshift($params, $this);

        return static::callMethod($this, $method, $params);
    }

    /**
     * Call a raw method
     *
     * @param  static|$this $objectOrClass
     * @param  string $method
     * @param  array $params
     * @return mixed
     */
    private static function callMethod($objectOrClass, $method, $params)
    {
        $method .= 'Raw';

        if (!method_exists($objectOrClass, $method)) {
            throw new BadMethodCallException("Method {$method} does not exist.");
        }

        return call_user_func_array([$objectOrClass, $method], $params);
    }
}
