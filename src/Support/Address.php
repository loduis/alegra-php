<?php

namespace Alegra\Support;

use Alegra\Application;

class Address extends \Illuminate\Api\Resource\Model
{
    protected $fillable = [
        'address'        => 'string',
        'city'           => 'string',
        'street'         => 'string',
        'exteriorNumber' => 'string',
        'interiorNumber' => 'string',
        'colony'         => 'string',
        'country'        => 'string',
        'locality'       => 'string',
        'municipality'   => 'string',
        'state'          => 'string',
        'zipCode'        => 'string'
    ];

    /**
     * Create a new price.
     *
     * @param  mixed    $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        if (is_string($attributes)) {
            $attributes = [
                'address' => $attributes
            ];
        }

        if (!Application::isMexico()) {
            $this->fillable = array_slice($this->fillable, 0, 2, true);
        }

        parent::__construct($attributes);
    }

    protected static function visible()
    {
        if (Application::isMexico()) {
            return ['*'];
        }

        return ['address', 'city'];
    }
}
