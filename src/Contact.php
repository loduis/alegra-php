<?php

namespace Alegra;

class Contact extends Resource
{
    const TYPE_CUSTOMER = 'client';

    const TYPE_SUPPLIER = 'provider';

    protected static $path = 'contacts';

    public function setTypeAttribute($value)
    {
        $value = array_values((array) $value);
        if (!($type = $this->getAttribute('type'))) {
            $this->attributes['type'] = $value;
        } else {
            $type = array_values((array) $type);
            $this->attributes['type'] = array_unique(array_merge($type, $value));
        }
    }
}
