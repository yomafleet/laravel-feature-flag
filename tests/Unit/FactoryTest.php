<?php

namespace Yomafleet\FeatureFlag\Tests\Unit;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Yomafleet\FeatureFlag\Clients\DisabledToggler;
use Yomafleet\FeatureFlag\Clients\Flipt;
use Yomafleet\FeatureFlag\Clients\Unleash;
use Yomafleet\FeatureFlag\Exceptions\UserNotProvidedException;
use Yomafleet\FeatureFlag\Facade;
use Yomafleet\FeatureFlag\Factory;
use Yomafleet\FeatureFlag\Middleware;
use Yomafleet\FeatureFlag\Tests\TestCase;

class FactoryTest extends TestCase
{
    public function test_make_with_disabled_toggler()
    {
        Config::set('feature-flags.disable', true);

        $toggler = Factory::make();

        $this->assertTrue($toggler instanceof DisabledToggler);
    }

    public function test_make_with_unleash_provider()
    {
        Config::set('feature-flags.default', 'unleash');
        $this->mockAuthUser();

        $toggler = Factory::make();

        $this->assertTrue($toggler instanceof Unleash);
    }

    public function test_make_with_flipt_provider()
    {
        Config::set('feature-flags.default', 'flipt');
        $this->mockAuthUser();

        $toggler = Factory::make();

        $this->assertTrue($toggler instanceof Flipt);
    }

    public function test_make_provider_without_user_throws_exception()
    {
        $this->expectException(UserNotProvidedException::class);
        new Flipt();

        $this->expectException(UserNotProvidedException::class);
        new Unleash();
    }

    public function test_factory_works_via_facade()
    {
        Config::set('feature-flags.default', 'disabled-toggler');
        Config::set('feature-flags.providers.disabled-toggler.optimistic', true);
        $isEnabled = Facade::enabled('something');

        $this->assertTrue($isEnabled);
    }

    public function test_works_via_middleware_success()
    {
        Config::set('feature-flags.default', 'disabled-toggler');
        Config::set('feature-flags.providers.disabled-toggler.optimistic', true);

        $middleware = new Middleware();
        $enabled = $middleware->handle(new Request(), fn ($req) => true, 'testing');
        $this->assertTrue($enabled);
    }

    public function test_works_via_middleware_fail()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        Config::set('feature-flags.default', 'disabled-toggler');
        Config::set('feature-flags.providers.disabled-toggler.optimistic', false);
        $middleware = new Middleware();
        $middleware->handle(new Request(), fn ($req) => true, 'testing');
    }
}
