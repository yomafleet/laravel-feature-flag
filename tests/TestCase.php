<?php

namespace Yomafleet\FeatureFlag\Tests;

use Yomafleet\FeatureFlag\ServiceProvider;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Auth;
use Yomafleet\FeatureFlag\UserContract;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        tap($app['config'], function (Repository $config) {
            $config->set(
                'feature-flags.providers',
                [
                    'flipt' => [
                        'namespace' => 'default',
                        'host' => 'http://localhost:8080',
                        'token' => null,
                    ],
                    'unleash' => [
                        'name' => 'default',
                        'url' => 'http://localhost:4242/api/',
                        'id' => 'EXAMPLE',
                        'token' => 'EXAMPLE',
                    ],
                    'disabled-toggler' => [
                        'optimistic' => true
                    ]
                ]
            );
        });
    }

    /**
     * Mock auth user
     *
     * @param array $attr
     * @return void
     */
    protected function mockAuthUser(array $attr = [])
    {
        Auth::shouldReceive('user')->andReturn($this->mockUser($attr));
    }

    /**
     * Mock user
     *
     * @param array $attr
     * @return UserContract
     */
    protected function mockUser(array $attr = [])
    {
        return new class ($attr) implements UserContract {
            public function __construct(protected array $attr)
            {
            }
            public function idKey(): string|int
            {
                return $this->attr['id'] ?? 1;
            }
            public function roleList(): array
            {
                return $this->attr['roles'] ?? [];
            }
            public function hasRoleAssigned(string $name): bool
            {
                return $this->attr['hasRole'] ?? false;
            }
        };
    }
}
