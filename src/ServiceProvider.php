<?php

namespace Yomafleet\FeatureFlag;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider as BaseProvider;

class ServiceProvider extends BaseProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    protected $defer = false;

    public function boot()
    {
        $source = realpath($raw = __DIR__ . '/../config/feature-flags.php') ?: $raw;

        $this->publishes([$source => config_path('feature-flags.php')]);

        $this->mergeConfigFrom($source, 'feature-flags');

        Blade::if('feature', function ($name) {
            return Facade::enabled($name);
        });

        /** @var \Illuminate\Routing\Router $router */
        $router = $this->app['router'];
        $router->aliasMiddleware('feature', Middleware::class);
        Route::macro('feature', function ($name) {
            /** @var \Illuminate\Routing\Route $this */
            return $this->middleware("feature:{$name}");
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('feature-toggler', function ($app) {
            return Factory::make();
        });
    }
}
