<?php

namespace Yomafleet\FeatureFlag\Clients;

use Yomafleet\FeatureFlag\FlaggableContract;

class Flipt implements FlaggableContract
{
    /** @inheritDoc */
    public function enabled(string $key): bool
    {
        return true;
    }
}
