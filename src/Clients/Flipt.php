<?php

namespace Yomafleet\FeatureFlag\Clients;

use Flipt\Client\FliptClient;
use Yomafleet\FeatureFlag\UserContract;
use Flipt\Client\AuthenticationStrategy;
use Flipt\Client\ClientTokenAuthentication;
use Yomafleet\FeatureFlag\Exceptions\UserNotProvidedException;
use Yomafleet\FeatureFlag\FlaggableContract;

class Flipt implements FlaggableContract
{
    protected FliptClient $client;
    protected ?UserContract $user;

    public function __construct(?UserContract $user = null, ?FliptClient $client = null)
    {
        $this->user = $user ?? auth()->user();
        $this->client = $client;
    }

    /**
     * Build Flipt client.
     *
     * @return void
     */
    protected function ensureClient()
    {
        if (!$this->client) {
            $config = config('feature-flags.providers.flipt');
            $client = new FliptClient(
                $config['host'],
                $config['namespace'],
                $this->userContext(),
                $this->getUser()->idKey(),
                $config['token'] ? $this->useAuth($config['token']) : null
            );

            /**
             * Fix for the client referencing non-existed property
             *
             * @disregard P1009 Undefined type
             * @see \Flipt\Client\FliptClient::mergeRequestParams
             */
            $client->reference = '';

            $this->client = $client;
        }
    }

    /**
     * Use authentication strategy.
     *
     * @param string $token
     * @return AuthenticationStrategy
     */
    protected function useAuth(string $token): AuthenticationStrategy
    {
        return new ClientTokenAuthentication($token);
    }

    /**
     * User context provider.
     *
     * @return array
     */
    protected function userContext(): array
    {
        return [
            'admin' => $this->getUser()->hasRoleAssigned('admin')
        ];
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
     * User provider.
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

    /** @inheritDoc */
    public function enabled(string $key): bool
    {
        $this->ensureClient();

        $response = $this->client->boolean($key);
        // logger('flipt response', [
        //     $response->getFlagKey(),
        //     $response->getEnabled(),
        //     $response->getReason(),
        //     $response->getRequestDurationMillis(),
        //     $response->getRequestidKey(),
        //     $response->getTimestamp(),
        // ]);

        return $response->getEnabled();
    }

    public function __call($name, $arguments)
    {
        return $this->client->$name(...$arguments);
    }
}
