<?php

namespace Yomafleet\FeatureFlag\Tests\Unit;

use Illuminate\Support\Facades\Config;
use Yomafleet\FeatureFlag\Clients\DisabledToggler;
use Yomafleet\FeatureFlag\Clients\Flipt;
use Yomafleet\FeatureFlag\Clients\Unleash;
use Yomafleet\FeatureFlag\Factory;
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
}
