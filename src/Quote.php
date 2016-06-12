<?php

namespace Alegra;

/**
 * Class for manage quotes
 */
class Quote extends Resource
{
    use \Illuminate\Api\Http\Restable;

    /**
     * Resource path
     *
     * @var string
     */
    protected static $path = 'estimates';
}
