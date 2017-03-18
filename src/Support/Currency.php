<?php

namespace Alegra\Support;

class Currency extends \Illuminate\Api\Resource\Model
{
    protected static $visible = ['*'];

    protected $fillable = [
        'code'   => 'string',
        'symbol' => 'string'
    ];
}
