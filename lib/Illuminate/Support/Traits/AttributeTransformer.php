<?php

namespace Illuminate\Support\Traits;

use Illuminate\Support\Str;

trait AttributeTransformer
{

    /**
     * Get the casts array.
     *
     * @return array
     */
    public function getTransformers()
    {
        return static::getStaticProperty('transforms', []);
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
     * Transform an attribute from simple type to Model
     *
     * @param  string $key
     * @param  mixed $value
     * @return $this
     */
    protected function transformAttribute($key, $value)
    {
        $transformer = $this->getTransformer($key);

        if (is_callable($transformer)) {
            $value = $transformer($value, $key);
        } elseif (Str::endsWith($transformer, '[]')) {
            // this is an colllection of transformed
            $collectionClass = $this->getCollectionTransformerHandler();
            if (!$value instanceof $collectionClass) {
                $className = str_replace('[]', '', $transformer);
                $value = $collectionClass::makeOf($className, $value);
            }
        } elseif (!$value instanceof $transformer) {
            $value = new $transformer((array) $value);
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Handler for create collection of type
     *
     * @return \Illuminate\Support\Collection
     */
    abstract protected function getCollectionTransformerHandler();
}
