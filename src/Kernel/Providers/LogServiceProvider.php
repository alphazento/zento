<?php

namespace Zento\Kernel\Providers;

use Monolog\Logger as Monolog;
use Illuminate\Support\ServiceProvider;
use Zento\Kernel\Booster\Log\LogManager;

class LogServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('log', function () {
            return new LogManager($this->app);
        });
    }
}