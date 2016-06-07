<?php

namespace Alegra;

use Illuminate\Api\Http\Resource;
use Illuminate\Api\Http\Restable;

/**
 * Class for manage quotes
 */
class Quote extends Resource
{
    use Restable;

    /**
     * Resource path
     *
     * @var string
     */
    protected static $path = 'estimates';
}
