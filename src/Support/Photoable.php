<?php

namespace Alegra\Support;


/**
 * adds the ability to appeal to be sent by email
 *
 * @method bool image(string $file)
 * @method bool static image($id, string $file)
 */
trait Photoable
{
    use Attachable;

    protected static function macroPhotoHandler($resource, $file)
    {
        return static::upload($resource, 'image', $file);
    }
}
