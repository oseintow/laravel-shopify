# Laravel Shopify

Laravel Shopify is a simple package which helps to build robust integration into Shopify.

## Installation

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

## Configuration

Laravel Shopify requires connection configuration. You will need to publish vendor assets

    php artisan vendor:publish

This will create a shopify.php file in the config directory. You will need to set your **API_KEY** and **SECRET**

## Usage

To install/integrate a shop you will need to initiate an oauth authentication with the shopify API and this require three components.

They are:

    1. Shop URL (eg. example.myshopify.com)
    2. Scope (eg. write_products, read_orders, etc)
    3. Redirect URL (eg. http://mydomain.com/process_oauth_result)

This process will enable us to obtain the shops access token

```php5
use Oseintow\Shopify\Facades\Shopify;

Route::get("install_shop",function()
{
    $shopUrl = "example.myshopify.com";
    $scope = ["write_products","read_orders"];
    $redirectUrl = "http://mydomain.com/process_shopify_data";

    $shopify = Shopify::setShopUrl($shopUrl);
    return redirect()->to($shopify->getAuthorizeUrl($scope,$redirectUrl));
});
```

Let's retrieve access token

```php5
Route::get("process_oauth_result",function(\Illuminate\Http\Request $request)
{
    $shopUrl = "example.myshopify.com";
    $accessToken = Shopify::setShopUrl($shopUrl)->getAccessToken($request->code));

    dd($accessToken);
    
    // redirect to success page or billing etc.
});
```

To verify request(hmac)

```php5

public function verifyRequest(Request $request)
{
    $queryString = $request->getQueryString();

    if(Shopify::verifyRequest($queryString)){
        logger("verification passed");
    }else{
        logger("verification failed");
    }
}

```

To verify webhook(hmac)

```php5

public function verifyWebhook(Request $request)
{
    $data = $request->getContent();
    $hmacHeader = $request->server('HTTP_X_SHOPIFY_HMAC_SHA256');

    if (Shopify::verifyWebHook($data, $hmacHeader)) {
        logger("verification passed");
    } else {
        logger("verification failed");
    }
}

```

To access API resource use

```php5
Shopify::get("resource uri", ["query string params"]);
Shopify::post("resource uri", ["post body"]);
Shopify::put("resource uri", ["put body"]);
Shopify::delete("resource uri");
```

Let use our access token to get products from shopify.

**NB:** You can use this to access any resource on shopify (be it Product, Shop, Order, etc)

```php5
$shopUrl = "example.myshopify.com";
$accessToken = "xxxxxxxxxxxxxxxxxxxxx";
$products = Shopify::setShopUrl($shopUrl)->setAccessToken($accessToken)->get("admin/products.json");
```

To pass query params

```php5
// returns Collection
$shopify = Shopify::setShopUrl($shopUrl)->setAccessToken($accessToken);
$products = $shopify->get("admin/products.json", ["limit"=>20, "page" => 1]);
```

## Controller Example

If you prefer to use dependency injection over facades like me, then you can inject the Class:

```php5
use Illuminate\Http\Request;
use Oseintow\Shopify\Shopify;

class Foo
{
    protected $shopify;

    public function __construct(Shopify $shopify)
    {
        $this->shopify = $shopify;
    }

    /*
    * returns Collection
    */
    public function getProducts(Request $request)
    {
        $products = $this->shopify->setShopUrl($shopUrl)
            ->setAccessToken($accessToken)
            ->get('admin/products.json');

        $products->each(function($product){
             \Log::info($product->title);
        });
    }
}
```

## Miscellaneous

To get Response headers

```php5
Shopify::getHeaders();
```

To get specific header
```php5
Shopify::getHeader("Content-Type");
```

Check if header exist
```php5
if(Shopify::hasHeader("Content-Type")){
    echo "Yes header exist";
}
```

To get response status code or status message
```php5
Shopify::getStatusCode(); // 200
Shopify::getReasonPhrase(); // ok
```














