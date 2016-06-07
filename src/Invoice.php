<?php

namespace Alegra;

use Alegra\Support\Mailable;
use Illuminate\Api\Http\Resource;
use Illuminate\Api\Http\Restable;

class Invoice extends Resource
{
    use Restable;
    use Mailable;
}
