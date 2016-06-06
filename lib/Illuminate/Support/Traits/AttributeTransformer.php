<?php

namespace Illuminate\Support\Traits;

trait AttributeTransformer
{

    /**
     * Get the casts array.
     *
     * @return array
     */
    public function getTransformers()
    {
        return property_exists(static::class, 'transforms') ? static::$transforms : [];
    }

    /**
     * Determine whether an attribute should be transformed to a complex type.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasTransformer($key)
    {
        return array_key_exists($key, $this->getTransformers());
    }

    /**
     * Get the tranformer for a model attribute.
     *
     * @param  string  $key
     * @return string
     */
    protected function getTransformer($key)
    {
        return $this->getTransformers()[$key];
    }

    /**
     * Transfor an attribute from simple type to Model
     *
     * @param  string $key
     * @param  mixed $value
     * @return $this
     */
    protected function transformAttribute($key, $value)
    {
        $transformer = $this->getTransformer($key);

        if (is_callable($transformer)) {
            $transformer = $transformer->bindTo($this);
            return $transformer($value, $key);
        }

        $this->attributes[$key] = new $transformer($value);

        return $this;
    }
}
