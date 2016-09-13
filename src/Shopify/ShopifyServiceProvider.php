<?php

namespace Oseintow\Shopify;

use Config;
use Illuminate\Support\ServiceProvider;

class ShopifyServiceProvider extends ServiceProvider {
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/shopify.php' => config_path('shopify.php'),
        ]);

        $this->app->alias('Shopify', 'Oseintow\Shopify\Facades\Shopify');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['shopify'] = $this->app->share(function($app)
        {
            return new Shopify(Config::get('shopify.key'),Config::get('shopify.secret'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
