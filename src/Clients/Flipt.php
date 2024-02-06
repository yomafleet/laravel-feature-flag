<?php

namespace Yomafleet\FeatureFlag\Clients;

use Flipt\Client\FliptClient;
use Yomafleet\FeatureFlag\UserContract;
use Flipt\Client\AuthenticationStrategy;
use Flipt\Client\ClientTokenAuthentication;
use Yomafleet\FeatureFlag\FlaggableContract;

class Flipt implements FlaggableContract
{
    protected FliptClient $client;
    protected ?UserContract $user;

    public function __construct(?UserContract $user = null)
    {
        $this->user = $user;
        $this->client = $this->buildClient();
    }

    /**
     * Build Flipt client.
     *
     * @return FliptClient
     */
    protected function buildClient(): FliptClient
    {
        $config = config('feature-flags.providers.flipt');
        $client = new FliptClient(
            $config['host'],
            $config['namespace'],
            $this->userContext(),
            $this->getUser()->id,
            $config['token'] ? $this->useAuth($config['token']) : null
        );

        /**
         * Fix for the client referencing non-existed property
         *
         * @disregard P1009 Undefined type
         * @see \Flipt\Client\FliptClient::mergeRequestParams
         */
        $client->reference = '';

        return $client;
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
            'admin' => $this->getUser()->hasRole('admin')
        ];
    }

    /**
     * User provider.
     *
     * @return UserContract
     */
    protected function getUser(): UserContract
    {
        /** @var UserContract $user */
        return $this->user === null || !$this->user->id() ? auth()->user() : $this->user;
    }

    /** @inheritDoc */
    public function enabled(string $key): bool
    {
        $response = $this->client->boolean($key);
        // logger('flipt response', [
        //     $response->getFlagKey(),
        //     $response->getEnabled(),
        //     $response->getReason(),
        //     $response->getRequestDurationMillis(),
        //     $response->getRequestId(),
        //     $response->getTimestamp(),
        // ]);

        return $response->getEnabled();
    }

    public function __call($name, $arguments)
    {
        return $this->client->$name(...$arguments);
    }
}
