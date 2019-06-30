<?php

namespace Samanar\Map\Facades;

use Illuminate\Support\Facades\Facade;

class Map extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'map';
    }
}
