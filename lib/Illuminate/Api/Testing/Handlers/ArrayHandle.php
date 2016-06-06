<?php

namespace Illuminate\Api\Testing\Handlers;

use Illuminate\Support\Collection;

class ArrayHandle
{
    private $resources;

    public function __construct()
    {
        $this->resources = new Collection();
    }

    public function get($path)
    {
        if (!$this->resources->has($path)) {
            $this->put($path, new Collection);
        }

        return $this->resources->get($path);
    }

    public function put($path, $value)
    {
        $this->resources->put($path, $value);
    }
}
