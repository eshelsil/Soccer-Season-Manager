<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Component\RegisterationManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('RegisterationManager', function ($app) {
            return new RegisterationManager();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
