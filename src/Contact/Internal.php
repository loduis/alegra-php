<?php

namespace Alegra\Contact;

class Internal extends \Illuminate\Api\Resource\Model
{
    /**
     * Define collection of this model
     */
    const collection = self::class  . '[]';

    /**
     * The fillable fields
     *
     * @var array
     */
    protected $fillable = [
        'name' => 'string',
        'lastName' => 'string',
        'email' => 'string',
        'phone' => 'string',
        'mobile' => 'string',
        'sendNotifications'=> 'boolean'
    ];

    /**
     * Attribute visibles for relations, all primary key are visible
     *
     * @var array
     */
    protected static $visible = ['*'];

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
                'name' => $attributes
            ];
        }

        parent::__construct($attributes);
    }
}
