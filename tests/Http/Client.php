<?php

namespace Alegra\Tests\Http;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

class Client
{
    private $resources = [];

    private $schemaPath;

    private static $instance;


    public function __construct($schemaPath)
    {
        $this->resources = new Collection;
        $this->schemaPath = $schemaPath;
    }

    /**
     * Create instance of the http client
     *
     * @param  string $schemaPath
     * @return static
     */
    public static function create($schemaPath)
    {
        if (!static::$instance) {
            static::$instance = new static($schemaPath);
        }

        return static::$instance;
    }

    /**
     * Invoke request using collection object
     *
     * @param  string $method
     * @param  string $path
     * @param  array $options
     * @return \GuzzleHttp\Psr7\Response
     */
    public function request($method, $path, $options)
    {
        $method = strtolower($method);

        return $this->$method($path, $options);
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
            $resources->put($path, $resources = $this->createResourceFromSchema($fetchPath));
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
        $resources   = $this->resources($path);
        $model       = $this->createResourceFromSchema($path, $options['json']);
        $model['id'] = count($resources) + 1;

        $resources->push($model);

        return new Response(201, [], $model->toJson(JSON_PRETTY_PRINT));
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
            $this->resources->put($path, new Collection);
        }

        return $this->resources->get($path);
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
