<?php

use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\RequestInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

require './vendor/autoload.php';

echo 'LIVE', PHP_EOL;

$handler = new JsonApiHandler(__DIR__ . '/tests/schemas');


$client = new HttpClient(array_merge(
        [
            'base_uri' => 'http://www.localhost.org/api/',
            'headers' => [
                'Accept' => 'application/json'
            ]
        ],
        [
                'handler' => $handler
        ]
));

$handler->request('POST /invoices/(\d+)/email', function ($request, $options) {
    return new Response(200);
});

$response = $client->post('invoices/10/email', ['json' => ['a' => 1, 'b' => '2']]);

echo $response->getBody(), PHP_EOL;


class JsonApiHandler
{
    private $resources = [];

    private $schemaPath;

    private $handlers = [
        'GET' => [

        ],
        'POST' => [

        ],
        'PUT' => [

        ],
        'PATCH' => [

        ],
        'DELETE' => [

        ]
    ];

    public function __construct($schemaPath)
    {
        $this->resources = new Collection;
        $this->schemaPath = $schemaPath;
    }

    public function __invoke(RequestInterface $request, array $options)
    {
        $method   = $request->getMethod();
        $uri      = $request->getUri();
        $basePath = $options['base_uri']->getPath();
        $path     = Str::substr($uri->getPath(), Str::length($basePath));
        if ($method === 'GET') {
            parse_str($uri->getQuery(), $options['query']);
        } else {
            $body = $request->getBody()->getContents();
            $options['json'] = json_decode($body, true);
        }

        if (($handler = $this->findHandler($method, $path))) {
            return $handler($request, $options);
        }

        $method = Str::lower($method);

        return $this->$method($path, $options);
    }

    /**
     * Register matcher of path
     *
     * @param  [type]   $expresion
     * @param  callable $callback
     * @return [type]
     */
    public function request($expresion, callable $callback)
    {
        list($method, $path) = explode(' ', $expresion);
        if (Str::startsWith($path, '/')) {
            $path = substr($path, 1);
        }
        $method = Str::upper($method);

        $this->handlers[$method][$path] = $callback;

        return $this;
    }

    protected function findHandler($method, $path)
    {
        $handlers = $this->handlers[$method];
        if (Arr::exists($handlers, $path)) {
            return $handler[$path];
        }

        foreach ($handlers as $regexPath => $handler) {
            if (preg_match('@^' . $regexPath . '$@', $path)) {
                return $handler;
            }
        }
    }

    /**
     * Response to get method
     *
     * @param  string $path
     * @param  array $options
     * @return \GuzzleHttp\Psr7\Response
     */
    private function get($path, $options)
    {
        list($path, $id) = static::parsePath($path);

        $resources = $this->resources($path);

        if ($id !== null) {
            $resources = $resources->where('id', $id)->first();
        } elseif (!count($resources) && $this->schemaExists(($fetchPath = $path . '.fetch'))) {
            $resources = $this->createResourceFromSchema($fetchPath);
            $this->putResource($path, $resources);
        }

        return new Response(200, [], $resources->toJson(JSON_PRETTY_PRINT));
    }

    /**
     * Response to post method
     *
     * @param  string $path
     * @param  array $options
     * @return \GuzzleHttp\Psr7\Response
     */
    private function post($path, $options)
    {
        $status = 200;
        $model = new Fluent();
        if (!Str::contains($path, '/')) {
            $resources   = $this->resources($path);
            $model       = $this->createResourceFromSchema($path, $options['json']);
            $model['id'] = count($resources) + 1;

            $resources->push($model);
        }

        return new Response($status, [], $model->toJson(JSON_PRETTY_PRINT));
    }

    private function put($path, $options)
    {
        list($path, $id) = static::parsePath($path);
        $resources   = $this->resources($path);

        if ($id !== null) {
            $resource = $resources->where('id', $id)->first();
            $resources->put($id, $this->merge($resource, $options));
        } else {
            $this->putResource($path, $resources = $this->merge($resources, $options));
        }

        return new Response(200, [], $resources->toJson(JSON_PRETTY_PRINT));
    }

    private function merge($resource, $options)
    {
        $resource = array_merge($resource->toArray(), $options['json']);

        return new Fluent($resource);
    }


    /**
     * Response to delete method
     *
     * @param  string $path
     * @param  array $options
     * @return \GuzzleHttp\Psr7\Response
     */
    private function delete($path, $options)
    {
        list($path, $id) = static::parsePath($path);

        $resources = $this->resources($path);

        $status = 200;

        $index = $resources->search(function ($item, $key) use ($id) {
            if ($item['id'] == $id) {
                return $key;
            }
        });

        if ($index !== null) {
            $resources->forget($index);
        } else {
            $status = 404;
        }

        return new Response($status, [], '{}');
    }

    private function resources($path)
    {
        if (!$this->resources->has($path)) {
            $this->putResource($path, new Collection);
        }

        return $this->resources->get($path);
    }

    private function putResource($path, $value)
    {
        $this->resources->put($path, $value);
    }

    private static function parsePath($path)
    {
        $id = null;
        if (Str::contains($path, '/')) {
            list($path, $id) = explode('/', $path);
        }

        return [$path, $id];
    }

    private function createResourceFromSchema($path, array $merge = [])
    {
        $filename = $this->schemaFile($path);
        $content = file_get_contents($filename);
        $data = json_decode($content, true);

        return new Fluent(array_merge($data, $merge));
    }

    private function schemaExists($path)
    {
        return file_exists($this->schemaFile($path));
    }

    private function schemaFile($path)
    {
        return $this->schemaPath . '/' . $path . '.json';
    }
}
