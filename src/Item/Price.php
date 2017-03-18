<?php

namespace Alegra\Item;

class Price extends \Illuminate\Api\Resource\Model
{
    /**
     * Define collection of this model
     */
    const collection = self::class  . '[]';

    /**
     * Primary key
     *
     * @var string
     */
    protected $primaryKey = [
        'idPriceList' => 'int'
    ];

    /**
     * Attribute casting
     *
     * @var array
     */
    protected $fillable = [
        'price' => 'float',
        'name'  => 'string',
        'type'  => 'string'
    ];

    /**
     * Attribute visibles for relations, all primary key are visible
     *
     * @var array
     */
    protected static $visible = [
        'price'
    ];

    /**
     * Create a new price.
     *
     * @param  mixed    $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        if (is_scalar($attributes)) {
            $attributes = [
                $this->getKeyName() => 1, // this is default price list
                'price'             => $attributes,
                'type'              => 'amount'
            ];
        }

        parent::__construct($attributes);
    }

    /**
     * Set mutator of id
     *
     * @param int $value
     */
    public function setIdAttribute($value)
    {
        return $this->setKey($value);
    }

    /**
     * Get mutator of id
     *
     * @return int
     */
    public function getIdAttribute()
    {
        return $this->getKey();
    }
}
