<?php

namespace Illuminate\Support\Traits;

use DomainException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

trait AttributeFillable
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Fill the model with an array of attributes.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function fill(array $attributes = [])
    {
        $this->attributes = [];

        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Determine if the given attribute may be mass assigned.
     *
     * @param  string  $key
     * @return bool
     */
    public function isFillable($key)
    {
        $fillable = $this->getFillable() ?: ['*'];

        if ($fillable == ['*']) {
            return true;
        }

        // Add primary key to fillable
        if (method_exists($this, 'getKeyName')) {
            $fillable[$this->getKeyName()] = $this->getKeyType();
        }

        // If the key is in the "fillable" array, we can of course assume that it's
        // a fillable attribute. Otherwise, we will check the guarded array when
        // we need to determine if the attribute is black-listed on the model.
        if (array_key_exists($key, $fillable)) {
            return true;
        }

        return empty($fillable) && ! Str::startsWith($key, '_');
    }

    /**
     * Get the fillable attributes for the model.
     *
     * @return array
     */
    public function getFillable()
    {
        if ($fillable = $this->fillable) {
            if (Arr::isAssoc($fillable)) {
                return $fillable;
            }

            $keys = array_values($fillable);
            $types = array_fill(0, count($keys), 'string');

            return array_combine(
                $keys,
                $types
            );
        }
    }

    /**
     * Set the fillable attributes for the model.
     *
     * @param  array  $fillable
     * @return $this
     */
    public function fillable($key, $type = 'string')
    {
        $this->fillable[$key] = $type;

        return $this;
    }

    /**
     * Validate if the attribute is fillable
     *
     * @param  string $attribute
     * @return void
     * @throws DomainException
     */
    protected function validateFillable($attribute)
    {
        // The developers may choose to place some attributes in the "fillable"
        // array, which means only those attributes may be set through mass
        // assignment to the model, and all others will just be ignored.
        if (!$this->isFillable($attribute)) {
            throw new DomainException('You not can set attribute: ' . $attribute);
        }
    }
}
