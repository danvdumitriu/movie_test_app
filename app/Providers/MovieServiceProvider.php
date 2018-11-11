<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\Services\MovieHelper;


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
        $this->app->bind('App\Library\Services\MovieHelper', function ($app) {
            return new MovieHelper();
        });
    }
}
