<?php

namespace Alegra;

use Alegra\Http\Resource;
use Alegra\Http\Restable;
use Alegra\Http\Mailable;

class Invoice extends Resource
{
    use Restable;
    use Mailable;
}
