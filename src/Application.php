<?php

namespace Alegra;

final class Application
{
    const VERSION_COLOMBIA = 'colombia';

    const VERSION_MEXICO = 'mexico';

    private static $version;

    public static function version()
    {
        return static::$version || (static::$version = Company::get()->applicationVersion);
    }

    public static function isColombia()
    {
        return static::$version === static::VERSION_COLOMBIA;
    }

    public static function isMexico()
    {
        return static::$version === static::VERSION_MEXICO;
    }
}
