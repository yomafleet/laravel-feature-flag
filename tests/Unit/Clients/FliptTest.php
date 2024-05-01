<?php

namespace Yomafleet\FeatureFlag\Tests\Unit\Clients;

use Mockery;
use Mockery\MockInterface;
use Flipt\Client\FliptClient;
use Yomafleet\FeatureFlag\Clients\Flipt;
use Flipt\Models\BooleanEvaluationResult;
use Yomafleet\FeatureFlag\Tests\TestCase;
use Yomafleet\FeatureFlag\Exceptions\UserNotProvidedException;

class FliptTest extends TestCase
{
    public function test_delegate_enable_check_to_flipt_client()
    {
        $this->mockAuthUser();
        $client = $this->mockClient();

        $flipt = new Flipt(null, $client);
        $result = $flipt->enabled('something');

        $this->assertTrue($result);
    }

    protected function mockClient(): FliptClient
    {
        /** @var FliptClient|MockInterface */
        $client = Mockery::mock(FliptClient::class);
        $response = Mockery::mock(BooleanEvaluationResult::class);
        $response->shouldReceive('getEnabled')->andReturn(true);
        $client->shouldReceive('boolean')->with('something')->andReturn($response);

        return $client;
    }

    public function test_flipt_error_with_user_provided()
    {
        $this->expectException(UserNotProvidedException::class);

        $client = $this->mockClient();
        $flipt = new Flipt(null, $client);

        $flipt->getUser();
    }

    public function test_flipt_can_set_user_later()
    {
        $user = $this->mockUser(['id' => 10]);

        $client = $this->mockClient();
        $flipt = new Flipt(null, $client);
        $flipt->setUser($user);

        $this->assertEquals($flipt->getUser()->idKey(), 10);
    }
}
