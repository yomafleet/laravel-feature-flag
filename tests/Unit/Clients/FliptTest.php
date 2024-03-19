<?php

namespace Yomafleet\FeatureFlag\Tests\Clients;

use Mockery;
use Flipt\Client\FliptClient;
use Flipt\Models\BooleanEvaluationResult;
use Yomafleet\FeatureFlag\Clients\Flipt;
use Yomafleet\FeatureFlag\Tests\TestCase;

class FliptTest extends TestCase
{
    public function test_delegate_enable_check_to_flipt_client()
    {
        $this->mockAuthUser();
        $client = Mockery::mock(FliptClient::class);
        $response = Mockery::mock(BooleanEvaluationResult::class);
        $response->shouldReceive('getEnabled')->andReturn(true);
        $client->shouldReceive('boolean')->with('something')->andReturn($response);

        $flipt = new Flipt(null, $client);
        $result = $flipt->enabled('something');

        $this->assertTrue($result);
    }
}
