<?php

namespace Zento\ThemeManager\Providers\Facades;

class ThemeManager extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'theme.manager';
    }
}