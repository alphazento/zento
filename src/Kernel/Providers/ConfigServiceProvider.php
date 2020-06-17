<?php

namespace Zento\Kernel\Providers;

use Zento\Kernel\Booster\Config\StoreConfig;
use Zento\Kernel\Facades\PackageManager;

class ConfigServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        if ($this->app->bound('config') && $this->app->bound('packagemanager')) {
            if (PackageManager::isKernelEnabled()) {
                $this->app->singleton('store_config_service', new StoreConfig());
            }
        }
    }
}
