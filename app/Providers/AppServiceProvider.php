<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ToolService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('tool', function ($app) {
            return new ToolService();
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
