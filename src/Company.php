<?php

namespace Alegra;

final class Company extends \Illuminate\Api\Http\Resource
{
    /**
     * The fillable fields
     *
     * @var array
     */
    protected $fillable = [
        'name'               => 'string',
        'identification'     => 'string',
        'phone'              => 'string',
        'website'            => 'string',
        'email'              => 'string',
        'regime'             => 'string',
        'currency'           => Support\Currency::class,
        'multicurrency'      => 'bool',
        'decimalPrecision'   => 'int',
        'invoicePreferences' => 'array',
        'applicationVersion' => 'string',
        'registryDate'       => 'datetime',
        'address'            => Support\Address::class
    ];

    /**
     * The resource path
     *
     * @var string
     */
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
