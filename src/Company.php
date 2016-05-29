<?php

namespace Alegra;

use Alegra\Http\Resource;

class Company extends Resource
{
    protected static $path = 'company';

    protected static $privateMethods = [
        'all',
        'delete',
        'save'
    ];

    public static function fetch($id = null)
    {
        return parent::fetch(null);
    }
}
