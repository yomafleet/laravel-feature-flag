<?php

namespace Yomafleet\FeatureFlag\Tests\Unit;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Blade;
use Yomafleet\FeatureFlag\Tests\TestCase;

class UtilitiesTest extends TestCase
{
    public function test_blade_directive_register()
    {
        $dirs = Blade::getCustomDirectives();

        $this->assertTrue(array_key_exists('feature', $dirs));
        $this->assertTrue(array_key_exists('endfeature', $dirs));
        $this->assertTrue(array_key_exists('elsefeature', $dirs));
        $this->assertTrue(array_key_exists('unlessfeature', $dirs));
    }

    public function test_middleware_register()
    {
        $middlewares = $this->app['router']->getMiddleware();

        $this->assertTrue(array_key_exists('feature', $middlewares));
    }

    public function test_macro_register()
    {
        $this->assertTrue(Route::hasMacro('feature'));
    }
}
