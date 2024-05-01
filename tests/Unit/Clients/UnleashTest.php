<?php

namespace Yomafleet\FeatureFlag\Tests\Unit\Clients;

use Mockery;
use Yomafleet\FeatureFlag\Tests\TestCase;
use Yomafleet\FeatureFlag\Clients\Unleash;
use Unleash\Client\Unleash as UnleashClient;
use Yomafleet\FeatureFlag\Exceptions\UserNotProvidedException;

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

    public function test_unleash_error_with_user_provided()
    {
        $client = Mockery::mock(UnleashClient::class);
        $client->shouldReceive('isEnabled')->andReturn(true);

        $this->expectException(UserNotProvidedException::class);

        $unleash = new Unleash(null, $client);
        $unleash->getUser();
    }

    public function test_unleash_can_set_user_later()
    {
        $user = $this->mockUser(['id' => 10]);

        $client = Mockery::mock(UnleashClient::class);
        $client->shouldReceive('isEnabled')->andReturn(true);

        $unleash = new Unleash(null, $client);
        $unleash->setUser($user);

        $this->assertEquals($unleash->getUser()->idKey(), 10);
    }
}
