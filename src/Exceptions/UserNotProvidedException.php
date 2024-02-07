<?php

namespace Yomafleet\FeatureFlag\Exceptions;

use Exception;

class UserNotProvidedException extends Exception
{
    protected string $defaultMessage = 'User not provided.';

    public function __construct($message = '', $code = 0)
    {
        parent::__construct($message ?: $this->defaultMessage, $this->code);
    }
}
