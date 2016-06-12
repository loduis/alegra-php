<?php

namespace Alegra;

use Illuminate\Api\Resource\Model;

class Price extends Model
{
    /**
     * Primary key
     *
     * @var string
     */
    protected $primaryKey = 'idPriceList';

    /**
     * Attribute casting
     *
     * @var array
     */
    protected static $casts = [
        'price' => 'float'
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
