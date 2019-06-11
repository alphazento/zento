<?php

namespace Zento\Kernel\Facades;

class InnerApiClient extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'innerapiclient';
    }
}