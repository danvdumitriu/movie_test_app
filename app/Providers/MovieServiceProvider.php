<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\Services\DataParser;


class MovieServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Library\Services\DataParser', function ($app) {
            return new DataParser();
        });
    }
}
