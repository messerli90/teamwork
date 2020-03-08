<?php

namespace Messerli90\Teamwork\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Messerli90\Teamwork\Skeleton\SkeletonClass
 */
class Teamwork extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'teamwork';
    }
}
