<?php

namespace Illuminate\Api\Testing;

use ReflectionMethod;
use ReflectionFunction;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\TransferStats;
use Illuminate\Support\Collection;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Promise\RejectedPromise;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Api\Testing\Handlers\FileHandle;
use Illuminate\Api\Testing\Handlers\ArrayHandle;
use Illuminate\Api\Testing\Handlers\CallableHandle;

class ApiHandler
{
    private $files;

    private $collections;

    private $requestCallable;

    private $responseCallable;

    private $originalResource;

    public function __construct($schemaPath, $separator = '.')
    {
        $this->files = new FileHandle($schemaPath, $separator);
        $this->collections = new ArrayHandle;
        $this->requestCallable = new CallableHandle;
        $this->responseCallable = new CallableHandle;
    }

    public static function create($schemaPath)
    {
        return new static($schemaPath);
    }

    public function __invoke(RequestInterface $request, array $options)
    {
        list($method, $path) = $this->prepareRequest($request, $options);
        $this->originalResource = [];
        $response = null;
        if (($handler = $this->requestCallable->find($method, $path))) {
            $response = $handler($request, $options);
            if ($response instanceof RequestInterface) {
                $request = $response;
            } elseif (is_numeric($response)) {
                $response = $this->createResponse($response);
            }
        }

        if ((!$response instanceof ResponseInterface) && (!$response instanceof \Exception)) {
            $method = Str::lower($method);

            $response = $this->$method($path, $options);
        }

        return $this->handler($method, $path, $response, $request, $options);
    }

    private function invokeStats(
        RequestInterface $request,
        array $options,
        ResponseInterface $response = null,
        $reason = null
    ) {
        if (isset($options['on_stats'])) {
            $stats = new TransferStats($request, $response, 0, $reason);
            call_user_func($options['on_stats'], $stats);
        }
    }

    private function handler($method, $path, $response, $request, $options)
    {
        $response = $response instanceof \Exception
            ? new RejectedPromise($response)
            : \GuzzleHttp\Promise\promise_for($response);

        return $response->then(
            function ($value) use ($request, $method, $path, $options) {
                $this->invokeStats($request, $options, $value);
                // custom handle response.
                if (($handler = $this->responseCallable->find($method, $path))) {
                    return $handler($value, $request, $this->originalResource, $options);
                }

                return $value;
            },
            function ($reason) use ($request, $options) {
                $this->invokeStats($request, $options, null, $reason);
                return new RejectedPromise($reason);
            }
        );
    }


    /**
     * Register request handler
     *
     * @param  [type]   $expresion
     * @param  callable $callback
     * @return [type]
     */
    public function request($message, callable $callback)
    {
        if (!is_callable($callback, false, $callableName)) {
            throw new InvalidArgumentException('The callback parameter is not callable');
        }

        $reflectionClass = 'Reflection' . (Str::contains($callableName, '::') ? 'Method' : 'Function');
        $reflection = new $reflectionClass($callableName);

        if ($reflection->getNumberOfParameters() > 0) {
            $parameters = $reflection->getParameters();
            $interface = $parameters[0]->getClass();
            if ($interface->name === ResponseInterface::class) {
                $this->responseCallable->put($message, $callback);
                return $this;
            }
        }

        $this->requestCallable->put($message, $callback);

        return $this;
    }

    /**
     * Response to get method
     *
     * @param  string $path
     * @param  array $options
     * @return \GuzzleHttp\Psr7\Response
     */
    protected function get($path, $options)
    {
        list($path, $id) = static::parsePath($path);

        $resources = $this->collections->get($path);
        if (!count($resources) && ($fileStorage = $this->files->find('get', $path))) {
            if (!Arr::isAssoc($fileStorage)) {
                $fileStorage = Collection::make($fileStorage);
            }
            $this->collections->put($path, $resources = $fileStorage);
        }

        if ($id !== null) {
            $resources = $this->first($resources, $id);
        } elseif ($options['query']) {
            $resources = $this->filterApply($resources, $options['query']);
        }

        $status = count($resources) > 0 ? 200 : 404;

        return $this->createResponse($status, $resources);
    }

    /**
     * Response to post method
     *
     * @param  string $path
     * @param  array $options
     * @return \GuzzleHttp\Psr7\Response
     */
    protected function post($path, $options)
    {
        list($path, $id) = static::parsePath($path);

        if ($id === null) {
            $status         = 201;
            $resources      = $this->collections->get($path);
            $resource       = $this->files->merge('post', $path, $options['json']);
            $resource['id'] = count($resources) + 1;
            $resources->push($resource);
        } else {
            $status   = 200;
            $resource = [];
        }

        return $this->createResponse($status, $resource);
    }

    protected function put($path, $options)
    {
        list($path, $id) = static::parsePath($path);

        $resources = $this->collections->get($path);

        $status = 200;

        if ($id !== null) {
            if ($resource = $this->first($resources, $id)) {
                $this->originalResource = $resource;
                $resources->put($id, $this->merge($resource, $options));
                $resources = $resources->get($id);
            } else {
                $status = 404;
            }
        } else {
            if (($count = count($resources)) == 0) {
                if ($resources = $this->files->find('get', $path)) {
                    $count = 1;
                }
            }
            if ($count) {
                $this->originalResource = $resources;
                $this->collections->put($path, $resources = $this->merge($resources, $options));
            } else {
                $status = 404;
            }
        }

        return $this->createResponse($status, $resources);
    }

    /**
     * Response to delete method
     *
     * @param  string $path
     * @param  array $options
     * @return \GuzzleHttp\Psr7\Response
     */
    protected function delete($path)
    {
        list($path, $id) = static::parsePath($path);

        $resources = $this->collections->get($path);

        $status = 200;

        $index = $resources->search(function ($item, $key) use ($id) {
            if ($item['id'] == $id) {
                return $key;
            }
        });
        $resource = [];
        if ($index !== null) {
            $resources->forget($index);
        } else {
            $status = 404;
        }

        return $this->createResponse($status, $resource);
    }

    private function merge($resource, $options)
    {
        return FileHandle::arrayMerge($resource, (array) $options['json']);
    }

    protected function prepareRequest($request, array &$options)
    {
        $method   = $request->getMethod();
        $uri      = $request->getUri();
        $basePath = $options['base_uri']->getPath();
        $path     = Str::substr($uri->getPath(), Str::length($basePath));
        if ($method === 'GET') {
            parse_str($uri->getQuery(), $options['query']);
        } else {
            $body = (string) $request->getBody();
            $options['json'] = json_decode($body, true);
        }

        return [
            $method,
            $path
        ];
    }

    private static function parsePath($path)
    {
        $id = null;
        if (Str::contains($path, '/')) {
            list($path, $id) = explode('/', $path);
        }

        return [$path, $id];
    }

    private function first($resources, $id)
    {
        if ($id !== null) {
            return $resources->where('id', $id)->first();
        }
    }

    private function filterApply($resources, $filters)
    {
        $limit = (int) Arr::pull($filters, 'limit');
        if ($limit > 0) {
            $resources = $resources->slice(0, $limit);
        }
        foreach ($filters as $key => $value) {
            $resources = $this->filter($resources, $key, $value);
        }

        return $resources->values();
    }

    private function filter($resources, $searchKey, $searchValue)
    {
        return $resources->filter(function ($value) use ($searchKey, $searchValue) {
            if (array_key_exists($searchKey, $value)) {
                $value = $value[$searchKey];
                if (is_scalar($value)) {
                    return $value == $searchValue;
                }
                return in_array($searchValue, (array) $value);
            }
        });
    }

    public function createResponse($status, $resource = [])
    {
        return new Response($status, ['Content-Type' => 'application/json'], json_encode($resource, JSON_PRETTY_PRINT));
    }

    public function createError($code, $request, $message = null)
    {
        if (is_array($message)) {
            $message = json_encode($message);
        }
        return RequestException::create(
            $request,
            new Response($code, ['Content-Type' => 'application/json'], $message)
        );
    }
}
