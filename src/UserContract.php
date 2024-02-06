<?php

namespace Yomafleet\FeatureFlag;

interface UserContract
{
    /**
     * Get ID of the user.
     *
     * @return string|integer
     */
    public function id(): string|int;

    /**
     * Get roles of the user.
     *
     * @return array
     */
    public function roles(): array;
}
