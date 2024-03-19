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
     * Get all roles of the user.
     *
     * @return array
     */
    public function roles(): array;

    /**
     * Check if the user has given role.
     *
     * @param string $name
     * @return boolean
     */
    public function hasRole(string $name): bool;
}
