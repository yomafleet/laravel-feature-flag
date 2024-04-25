<?php

namespace Yomafleet\FeatureFlag;

interface UserContract
{
    /**
     * Get ID of the user.
     *
     * @return string|integer
     */
    public function idKey(): string|int;

    /**
     * Get all roles of the user.
     *
     * @return array
     */
    public function roleList(): array;

    /**
     * Check if the user has given role.
     *
     * @param string $name
     * @return boolean
     */
    public function hasRoleAssigned(string $name): bool;
}
