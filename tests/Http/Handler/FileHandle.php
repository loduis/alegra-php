<?php

namespace Alegra\Tests\Http\Handler;

use Illuminate\Support\Fluent;
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
        return new Fluent(array_merge($this->get($path), $data));
    }

    public function fetch($path)
    {
        $path .= '.fetch';
        if ($this->exists($path)) {
            return new Fluent($this->get($path));
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
