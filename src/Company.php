<?php

namespace Alegra;

class Company extends \Illuminate\Api\Http\Resource
{
    protected $fillable = [
        'name'               => 'string',
        'identification'     => 'string',
        'phone'              => 'string',
        'website'            => 'string',
        'email'              => 'string',
        'regime'             => 'string',
        'currency'           => 'string',
        'multicurrency'      => 'bool',
        'decimalPrecision'   => 'int',
        'invoicePreferences' => 'array',
        'applicationVersion' => 'string',
        'registryDate'       => 'datetime',
        'address'            => Support\Address::class
    ];

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
