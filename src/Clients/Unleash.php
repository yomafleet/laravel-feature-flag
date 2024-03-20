<?php

namespace Yomafleet\FeatureFlag\Clients;

use League\Flysystem\Filesystem;
use Unleash\Client\UnleashBuilder;
use League\Flysystem\Adapter\Local;
use Psr\SimpleCache\CacheInterface;
use Unleash\Client\Unleash as Client;
use Yomafleet\FeatureFlag\UserContract;
use Unleash\Client\Configuration\Context;
use Yomafleet\FeatureFlag\FlaggableContract;
use Unleash\Client\Configuration\UnleashContext;
use Cache\Adapter\Filesystem\FilesystemCachePool;
use Unleash\Client\ContextProvider\UnleashContextProvider;
use Yomafleet\FeatureFlag\Exceptions\UserNotProvidedException;

/** @mixin \Unleash\Client\Unleash */
class Unleash implements FlaggableContract
{
    protected Client $client;
    protected UserContract $user;

    public function __construct(?UserContract $user = null, ?Client $client = null)
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            throw new UserNotProvidedException();
        }

        $this->user = $user;
        $this->client = $client ?? $this->buildClient();
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
            ->withContextProvider($this->userContextProvider())
            ->withCacheHandler(static::getCache(), 30)
            ->build();
    }

    /**
     * Create new user context.
     *
     * @return Context
     */
    protected function userContext(): Context
    {
        return new UnleashContext(
            currentUserId: $this->user->id(),
            customContext: ['roles' => implode(',', $this->user->roles())]
        );
    }

    /**
     * Create new user context provider.
     *
     * @return UnleashContextProvider
     */
    protected function userContextProvider()
    {
        $context = $this->userContext();

        return new class ($context) implements UnleashContextProvider {
            public function __construct(protected Context $context)
            {
            }

            public function getContext(): Context
            {
                return $this->context;
            }
        };
    }

    protected static function getCache(): CacheInterface
    {
        $cache = cache()?->store() ?? null;

        if ($cache instanceof CacheInterface) {
            return $cache;
        }

        return new FilesystemCachePool(
            new Filesystem(new Local(storage_path('framework/cache'))),
        );
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
