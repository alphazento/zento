<?php

namespace Zento\RouteAndRewriter\Facades;

class RouteAndRewriterService extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'routeandrewriter_svc';
    }
}