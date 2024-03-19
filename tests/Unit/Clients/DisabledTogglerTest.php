<?php

namespace Yomafleet\FeatureFlag\Tests\Clients;

use Illuminate\Support\Facades\Config;
use Yomafleet\FeatureFlag\Factory;
use Yomafleet\FeatureFlag\Tests\TestCase;

class DisabledTogglerTest extends TestCase
{
    public function test_disabled_toggler_change_as_configured()
    {
        Config::set('feature-flags.disable', true);

        Config::set('feature-flags.providers.disabled-toggler.optimistic', true);
        $toggler = Factory::make();

        $this->assertTrue($toggler->enabled('something'));

        Config::set('feature-flags.providers.disabled-toggler.optimistic', false);
        $toggler = Factory::make();

        $this->assertFalse($toggler->enabled('something'));
    }
}
