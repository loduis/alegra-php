<?php

namespace Alegra;

/**
 * Class for manage quotes
 */
class Quote extends Resource
{
    /**
     * Resource path
     *
     * @var string
     */
    protected static $path = 'estimates';

    /**
     * Add ability for support metadata
     */
    use Support\Metadata;
}
