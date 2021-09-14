<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HttpClientServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(\GuzzleHttp\ClientInterface::class, \GuzzleHttp\Client::class);
        //:end-bindings:
    }
}
