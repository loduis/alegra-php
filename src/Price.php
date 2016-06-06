<?php

namespace Alegra;

use Illuminate\Api\Resource\Model;

class Price extends Model
{
    protected $primaryKey = 'idPriceList';

    protected static $casts = [
        'id' => 'int',
        'price' => 'float'
    ];

    public function setIdAttribute($value)
    {
        return $this->setKey($value);
    }

    public function getIdAttribute()
    {
        return $this->getKey();
    }
}
