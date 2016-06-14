<?php

namespace Alegra;

class Company extends \Illuminate\Api\Http\Resource
{
    protected static $path = 'company';

    /**
     * Get the company resource
     *
     * @return static
     */
    public static function get()
    {
        return static::instanceFromRequest('GET');
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
