<?php

namespace Shopex\LubanSite\Providers;

use Illuminate\Support\ServiceProvider;
use Shopex\LubanSite\Site;

class LubanSiteProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(\Illuminate\Routing\Router $router)
    {

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('LubanSite',function(){
            return new Site;
        });
    }
}
