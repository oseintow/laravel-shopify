<?php

namespace Oseintow\Shopify\Facades;

use Illuminate\Support\Facades\Facade;

class Shopify extends Facade {

    protected static function getFacadeAccessor() { return 'shopify'; }

}