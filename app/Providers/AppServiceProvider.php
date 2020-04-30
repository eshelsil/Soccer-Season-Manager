<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Component\RegisteredTeamsManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('RegisteredTeamsManager', function ($app) {
            return new RegisteredTeamsManager();
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
