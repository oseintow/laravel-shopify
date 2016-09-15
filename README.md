# Laravel Shopify

Laravel Shopify is a simple package which helps to build robust integration into shopify.

##Installation

Add package to composer.json

    composer require oseintow/laravel-shopify

Add the service provider to config/app.php in the providers array.

    ```php5
    <?php

    'providers' => [
        ...
        Oseintow\Shopify\ShopifyServiceProvider::class,
    ],
    ```

Setup alias for the Facade

    ```php5
    <?php

        'aliases' => [
            ...
            'Shopify' => Oseintow\Shopify\Facades\Shopify::class,
        ],
    ```

##Configuration

Laravel Shopify requires connection configuration. You will need to publish vendor assets

    php artisan vendor:publish

This will create a shopify.php file in the config directory. You will need to set your **API_KEY** and **SECRET**

##Usage

To install/integrate a shop you will need to initiate an oauth authentication with the shopify API and this require three components.

They are:
    1. Shop url (eg. example.myshopify.com)
    2. scope (eg. write_products, read_orders, etc)
    2. redirect url (eg. mydomain.com/process_oauth_result)

This process will enable us to obtain the shops access token

    ```php5
    use Oseintow\Shopify\Facades\Shopify

    Route::get("install_shop",function(){
        $shopUrl = "example.myshopify.com";
        $scope = ["write_products","read_orders"];
        $redirectUrl = "mydomain.com/process_shopify_data";

        $shopify = Shopify::setShopUrl($shopUrl);
        return redirect()->to($shopify->getAuthorizeUrl($scope,$redirectUrl));
    });
    ```

Let retrieve access token

```php5
    Route::get("process_oauth_result",function(\Illuminate\Http\Request $request){
        $shopUrl = "example.myshopify.com";
        $accesToken = Shopify::setShopUrl($shopUrl)->getAccessToken($request->code));

        dd($accessToken);
    });
```














