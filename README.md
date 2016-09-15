## Laravel Shopify
---

Laravel Shopify is a simple package which helps to build robust integration into shopify.

#Installation
---

Add package to composer.json

    composer require oseintow/laravel-shopify

Add the service provider to config/app.php in the providers array.

    <?php

    'providers' => [
        ...
        Oseintow\Shopify\ShopifyServiceProvider::class,
    ],

Setup alias for the Facade

    <?php

        'aliases' => [
            ...
            'Shopify' => Oseintow\Shopify\Facades\Shopify::class,
        ],

#Configuration
---

Laravel Shopify requires connection configuration. You will need to publish vendor assets

    php artisan vendor:publish

This will create a shopify.php file in the config directory. You will need to set your *API_KKEY* and *SECRET*

#Usage
---












