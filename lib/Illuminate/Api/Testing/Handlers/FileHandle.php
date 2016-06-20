<?php

namespace Illuminate\Api\Testing\Handlers;

use Illuminate\Support\Collection;

class FileHandle
{
    private $directory;

    private $cache;

    private $separator;

    public function __construct($path, $separator = '.')
    {
        $this->directory = $path;
        $this->cache = new Collection();
        $this->separator = $separator;
    }

    public function merge($method, $path, array $data)
    {
        $path = $this->getPath($method, $path);
        return static::arrayMerge($this->get($path), $data);
    }

    public static function arrayMerge(array $array1, array $array2)
    {
        foreach ($array1 as $key => $value) {
            if (array_key_exists($key, $array2)) {
                if (is_array($value)) {
                    $array2[$key] = static::arrayMerge($value, (array) $array2[$key]);
                }
                continue;
            }
            $array2[$key] = $value;
        }

        return $array2;
    }

    public function find($method, $path)
    {
        $path = $this->getPath($method, $path);
        if ($this->exists($path)) {
            return $this->get($path);
        }
    }

    protected function getPath($method, $path)
    {
        return strtolower($method) . $this->separator . $path;
    }

    protected function get($path)
    {
        if ($cache = ($this->cache->get($path))) {
            return $cache;
        }

        $content = file_get_contents($this->file($path));

        $this->cache->put($path, $schema = json_decode($content, true));

        return $schema;
    }

    private function exists($path)
    {
        return file_exists($this->file($path));
    }

    private function file($path)
    {
        return $this->directory . '/' . $path . '.json';
    }
}
