<?php

namespace Zento\Kernel\Facades;

class Debugger extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'debugger';
    }
}
