<?php

namespace Yomafleet\FeatureFlag;

use Illuminate\Support\Facades\Facade as BaseFacade;

/**
 * @method static bool enabled(string $key)
 */
class Facade extends BaseFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'feature-toggler';
    }
}
