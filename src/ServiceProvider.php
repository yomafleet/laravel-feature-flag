<?php

namespace Yomafleet\FeatureFlag;

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
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('feature-toggler', function ($app) {
            return Factory::make();
        });
    }
}
