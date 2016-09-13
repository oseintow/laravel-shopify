<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Shopify Api
    |--------------------------------------------------------------------------
    |
    | This file is for setting the credentials for shopify api key and secret.
    |
    */

    'key' => env("SHOPIFY_APIKEY", null),
    'secret' => env("SHOPIFY_SECRET", null)
];