<?php

namespace Yomafleet\FeatureFlag;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Yomafleet\FeatureFlag\FlaggableContract;
use Yomafleet\FeatureFlag\Clients\DisabledToggler;

class Factory
{
    /**
     * Make a feature-flag toggler.
     *
     * @return FlaggableContract
     */
    public static function make(): FlaggableContract
    {
        if (Config::get('feature-flags.disable', false)) {
            return new DisabledToggler();
        }

        $provider = Config::get('feature-flags.default');
        $name = __NAMESPACE__ . '\\Clients\\' . Str::studly($provider);

        return App::make($name);
    }
}
