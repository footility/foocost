<?php

namespace Footility\FooCost\Http;

use Illuminate\Support\ServiceProvider;

class FooCostServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../views', 'foocost');
        $this->publishes([
            __DIR__ . '/../config/foocost.php' => config_path('foocost.php'),
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/foocost.php', 'foocost'
        );
    }
}
