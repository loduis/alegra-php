<?php

namespace Alegra\Item;

class Inventory extends \Illuminate\Api\Resource\Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected static $casts = [
        'unitCost' => 'float',
        'initialQuantity' => 'float'
    ];

    /**
     * The attributes that should be visible when the class is an tranforms.
     *
     * @var array
     */
    protected static $visible = [
        'unit',
        'unitCost',
        'initialQuantity'
    ];

    protected static $units = [
        'unit',
        'centimeter',
        'meter',
        'inch',
        'centimeterSquared',
        'meterSquared',
        'inchSquared',
        'mililiter',
        'liter',
        'gallon',
        'gram',
        'kilogram',
        'ton',
        'pound',
        'piece',
        'service',
        'notApplicable'
    ];

    /**
     * Shortcut for set attribute unitCost
     *
     * @param float $value
     */
    public function setCostAttribute($value)
    {
        $this->attributes['unitCost'] = (float) $value;
    }

    /**
     * Shortcut for set the attribute initialQuantity
     *
     * @param float $value
     */
    public function setInitialAttribute($value)
    {
        $this->attributes['initialQuantity'] = (float) $value;
    }

    /**
     * Shortcut for get attribute unitCost
     *
     * @return float
     */
    public function getCostAttribute()
    {
        return (float) $this->attributes['unitCost'];
    }

    /**
     * Shortcut for get the attribute initialQuantity
     *
     * @param float $value
     */
    public function getInitialAttribute()
    {
        return (float) $this->attributes['initialQuantity'];
    }
}
