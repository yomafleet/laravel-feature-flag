<?php

namespace Yomafleet\FeatureFlag\Tests\Clients;

use Mockery;
use Yomafleet\FeatureFlag\Tests\TestCase;
use Yomafleet\FeatureFlag\Clients\Unleash;
use Unleash\Client\Unleash as UnleashClient;

class UnleashTest extends TestCase
{
    public function test_delegate_enable_check_to_unleash_client()
    {
        $this->mockAuthUser();
        $client = Mockery::mock(UnleashClient::class);
        $client->shouldReceive('isEnabled')->andReturn(true);
        $unleash = new Unleash(null, $client);
        $result = $unleash->enabled('something');

        $this->assertTrue($result);
    }
}
