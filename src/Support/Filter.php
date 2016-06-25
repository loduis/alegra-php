<?php

namespace Alegra\Support;

use Illuminate\Api\Resource\Filter as ResourceFilter;

/**
 * Filter alegra request
 *
 * @method $this start(int $value)
 * @method $this limit(int $value)
 * @method $this orderDirection(string $value)
 * @method $this orderField(string $value)
 * @method $this metadata()
 */
class Filter extends ResourceFilter
{
    const ORDER_ASC = 'ASC';

    const ORDER_DESC = 'DESC';

    public static $snakeAttributes = true;

    protected $fillable = [
        'start'           => 'int',
        'limit'           => 'int',
        'order_direction' => 'string',
        'order_field'     => 'string',
        'query'           => 'string',
        'metadata'        => 'bool'
    ];
}
