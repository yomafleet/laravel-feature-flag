<?php

namespace Yomafleet\FeatureFlag\Clients;

use Yomafleet\FeatureFlag\FlaggableContract;

use League\Flysystem\Filesystem;
use Unleash\Client\UnleashBuilder;
use League\Flysystem\Adapter\Local;
use Unleash\Client\Unleash as Client;
use Yomafleet\FeatureFlag\UserContract;
use Unleash\Client\Configuration\Context;
use Unleash\Client\Configuration\UnleashContext;
use Cache\Adapter\Filesystem\FilesystemCachePool;
use Unleash\Client\ContextProvider\UnleashContextProvider;

/** @mixin \Unleash\Client\Unleash */
class Unleash implements FlaggableContract
{
    protected Client $client;
    protected ?UserContract $user;

    public function __construct(?UserContract $user = null)
    {
        $this->user = $user;
        $this->client = $this->buildClient();
    }

    /**
     * Build unleash client.
     *
     * @return Client
     */
    protected function buildClient(): Client
    {
        $config = config('feature-flags.providers.unleash');
        return UnleashBuilder::create()
            ->withAppName($config['name'])
            ->withAppUrl($config['url'])
            ->withInstanceId($config['id'])
            ->withHeader('Authorization', $config['token'])
            ->withContextProvider($this->userContextProvider($this->user))
            ->withCacheHandler(new FilesystemCachePool(
                new Filesystem(new Local(storage_path('framework/cache'))),
            ), 30)
            ->build();
    }

    /**
     * Create new user context.
     *
     * @param UserContract $user
     * @return Context
     */
    protected function userContext(UserContract $user): Context
    {
        return new UnleashContext(
            currentUserId: $user->id(),
            customContext: ['roles' => implode(',', $user->roles())]
        );
    }

    /**
     * Create new user context provider.
     *
     * @param UserContract|null $user
     * @return UnleashContextProvider
     */
    protected function userContextProvider(?UserContract $user = null)
    {
        $user = $user === null || !$user->id() ? auth()->user() : $user;
        $context = $user ? $this->userContext($user) : new UnleashContext();

        return new class ($context) implements UnleashContextProvider {
            public function __construct(protected Context $context) {}

            public function getContext(): Context
            {
                return $this->context;
            }
        };
    }

    /** @inheritDoc */
    public function enabled(string $key): bool
    {
        return $this->client->isEnabled($key);
    }

    public function __call($name, $arguments)
    {
        return $this->client->$name(...$arguments);
    }
}
