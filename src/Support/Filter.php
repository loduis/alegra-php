<?php

namespace Alegra\Support;

use Illuminate\Api\Resource\Parameter;

/**
 * Filter alegra request
 *
 * @method $this start(int $value)
 * @method $this limit(int $value)
 * @method $this orderDirection(string $value)
 * @method $this orderField(string $value)
 * @method $this metadata()
 */
class Filter extends Parameter
{
    const ORDER_ASC = 'ASC';

    const ORDER_DESC = 'DESC';

    public static $snakeAttributes = true;
}
