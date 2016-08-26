<?php

namespace AstritZeqiri\GRecaptcha;

use AstritZeqiri\GRecaptcha\GRecaptcha;
use Illuminate\Support\ServiceProvider;

class GRecaptchaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/grecaptcha.php' => config_path('grecaptcha.php'),
        ]);

        $this->publishes([
            __DIR__.'/assets' => public_path('vendor/grecaptcha'),
        ], 'public');

        $this->app['validator']->extend('grecaptcha', function ($attribute, $value, $parameters, $validator){
            return GRecaptcha::check($value);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
