<?php

namespace Alegra\Support;

class Currency extends \Illuminate\Api\Resource\Model
{
    protected $fillable = [
        'code'   => 'string',
        'symbol' => 'string'
    ];
}
