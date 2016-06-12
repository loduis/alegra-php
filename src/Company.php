<?php

namespace Alegra;

class Company extends Resource
{
    use \Illuminate\Api\Http\Restable {
        all as private;
        delete as private;
    }

    protected static $path = 'company';

    /**
     * Get the company resource
     *
     * @return static
     */
    public static function get()
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
