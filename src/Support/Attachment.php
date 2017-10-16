<?php

namespace Alegra\Support;

class Attachment extends \Illuminate\Api\Resource\Model
{
    /**
     * Define collection of this model
     */
    const collection = self::class  . '[]';

    protected static $visible = ['*'];

    protected $fillable = [
        'name'   => 'string',
        'url' => 'string'
    ];
}
