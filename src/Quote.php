<?php

namespace Alegra;

use Alegra\Http\Resource;
use Alegra\Http\Restable;

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
