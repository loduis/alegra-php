<?php

namespace Alegra\Contact;

use DomainException;
use Illuminate\Support\Collection;

trait Typeable
{
    public function __construct($attributes = [])
    {
        static::useDefaultType($attributes);

        parent::__construct($attributes);
    }

    public static function all($params = [])
    {
        $params['type'] = static::TYPE;

        return parent::all($params);
    }

    public static function create($params = [])
    {
        static::useDefaultType($params);

        return parent::create($params);
    }

    public static function get($id)
    {
        $contact = parent::get($id);

        static::checkType($contact->type);

        return $contact;
    }

    protected static function useDefaultType(& $container)
    {
        if (isset($container['type'])) {
            static::checkType($container['type']);
        } else {
            static::addType($container);
        }
    }

    protected static function addType(& $container)
    {
        $container['type'] = [
            static::TYPE
        ];
    }

    protected static function checkType($types)
    {
        $types = Collection::make($types);
        if (!$types->contains(static::TYPE)) {
            throw new DomainException('Invalid contact type: ' . $types->implode(','));
        }
    }
}
