<?php

namespace Alegra\Tests\Http\Handler;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;

/**
 * Class for manage handler on request
 */
class CallableHandle
{
    /**
     * Container of handlers
     *
     * @var [type]
     */
    private $handlers = [
        // Get handlers
        'GET' => [

        ],
        // Post handlers
        'POST' => [

        ],
        // Put handlers
        'PUT' => [

        ],
        // Patch handlers
        'PATCH' => [

        ],
        // Delete handlers
        'DELETE' => [

        ]
    ];

    /**
     * Put a request handler
     *
     * @param  string   $message
     * @param  callable $callback
     * @return $this
     */
    public function put($message, callable $callback)
    {
        list($method, $path) = explode(' ', $message);
        if (Str::startsWith($path, '/')) {
            $path = Str::substr($path, 1);
        }

        $method = Str::upper($method);
        $this->handlers[$method][$path] = $callback;

        return $this;
    }

    /**
     * Find a handler for manage a request
     *
     * @param  string $method
     * @param  string $path
     * @return null|callable
     */
    public function find($method, $path)
    {
        $method = Str::upper($method);
        $handlers = $this->handlers[$method];
        if (Arr::exists($handlers, $path)) {
            return $handlers[$path];
        }

        foreach ($handlers as $regexPath => $handler) {
            if (preg_match('@^' . $regexPath . '$@', $path)) {
                return $handler;
            }
        }
    }
}
