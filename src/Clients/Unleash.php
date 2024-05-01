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
    protected ?UserContract $user;

    public function __construct(?UserContract $user = null, ?Client $client = null)
    {
        $this->user = $user ?? auth()->user();
        $this->client = $client;
    }

    /**
     * Build unleash client.
     *
     * @return void
     */
    protected function ensureClient()
    {
        if (!$this->client) {
            $config = config('feature-flags.providers.unleash');
            $this->client = UnleashBuilder::create()
                ->withAppName($config['name'])
                ->withAppUrl($config['url'])
                ->withInstanceId($config['id'])
                ->withHeader('Authorization', $config['token'])
                ->withContextProvider($this->userContextProvider())
                ->withCacheHandler(static::getCache(), 30)
                ->build();
        }
    }

    /**
     * Sets the user.
     *
     * @param UserContract $user
     * @return static
     */
    public function setUser(UserContract $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @throws UserNotProvidedException
     * @return UserContract
     */
    public function getUser(): UserContract
    {
        if (!$this->user) {
            throw new UserNotProvidedException();
        }

        return $this->user;
    }

    /**
     * Create new user context.
     *
     * @throws UserNotProvidedException
     * @return Context
     */
    protected function userContext(): Context
    {
        $user = $this->getUser();

        return new UnleashContext(
            currentUserId: $user->idKey(),
            customContext: ['roles' => implode(',', $user->roleList())]
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
        $this->ensureClient();

        return $this->client->isEnabled($key);
    }

    public function __call($name, $arguments)
    {
        return $this->client->$name(...$arguments);
    }
}
