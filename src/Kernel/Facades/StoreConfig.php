<?php

namespace Zento\Kernel\Facades;

class StoreConfig extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'store_config_service';
    }
}
