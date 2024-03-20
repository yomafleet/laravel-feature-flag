<?php

namespace Yomafleet\FeatureFlag;

interface FlaggableContract
{
    /**
     * Determine whether give flag is enable or not.
     *
     * @param string $key
     * @return boolean
     */
    public function enabled(string $key): bool;
}
