<?php

namespace Yomafleet\FeatureFlag\Clients;

use Yomafleet\FeatureFlag\FlaggableContract;
use Illuminate\Support\Facades\Config;

class DisabledToggler implements FlaggableContract
{
    protected bool $optimistic;

    public function __construct()
    {
        $this->optimistic = Config::get('feature-flags.providers.disabled-toggler.optimistic', false);
    }

    /** @inheritDoc */
    public function enabled(string $key): bool
    {
        return $this->optimistic;
    }
}
