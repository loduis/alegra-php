<?php

namespace Alegra\Support;

use Illuminate\Support\Arr;

/**
 * adds the ability to appeal to be sent by email
 *
 * @method bool send(array $options)
 * @method bool static send($resource, array $options)
 * @method Mail mail($resource = null)
 */
trait Mailable
{
    /**
     * Send email the handler method
     *
     * @param  int|static $resource
     * @param  array $options
     * @return bool
     */
    protected static function macroSendHandler($resource, ...$options)
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
    protected static function macroMailHandler($resource)
    {
        if (!static::isResource($resource)) {
            $resource = new static($resource);
        }

        return new Mail($resource);
    }
}
