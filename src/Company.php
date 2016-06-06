<?php

namespace Alegra;

use Illuminate\Api\Http\Resource;
use Illuminate\Api\Http\Restable;

class Company extends Resource
{
    use Restable {
        all as private;
        delete as private;
    }

    protected static $path = 'company';

    /**
     * Fetch the company resource
     *
     * @return static
     */
    public static function fetch()
    {
        return static::createFromRequest('GET');
    }

    /**
     * Save company.
     *
     * @return $this
     */
    public function save()
    {
        return $this->store('PUT');
    }
}
