<?php

namespace Alegra\Tests\Http\Handler;

use Illuminate\Support\Collection;

class FileHandle
{
    private $directory;

    private $cache;

    public function __construct($path)
    {
        $this->directory = $path;
        $this->cache = new Collection();
    }

    public function merge($path, array $data)
    {
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

    public function fetch($path)
    {
        $path .= '.fetch';
        if ($this->exists($path)) {
            return $this->get($path);
        }
    }

    public function get($path)
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
