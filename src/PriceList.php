<?php

namespace Alegra;

final class PriceList extends Resource
{
    protected $fillable = [
        'name'       => 'string',
        'type'       => 'string',
        'status'     => 'string',
        "percentage" => "float"
    ];
}