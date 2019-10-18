<?php

namespace Zento\Kernel\Providers;

use Zento\Kernel\Booster\Config\Repository;
use Zento\Kernel\Facades\PackageManager;

class ConfigServiceProvider extends \Illuminate\Support\ServiceProvider 
{
    // public function register() {
    //     $this->app->booted(function ($app) {     
    //         $this->replaceConfigService();
    //     });
    // }

    public function boot() {
        $this->replaceConfigService();
    }

    /**
     * replace the config service.
     */
    public function replaceConfigService()
    {
        if ($this->app->bound('config') && $this->app->bound('packagemanager')) {
            if (PackageManager::isKernelEnabled()) {
                $configs = $this->app->make('config')->get('zento.Zento_Kernel.config_extend');
                $repositoryClass = $configs['extra_repository'] ?? null;
                if ($repositoryClass && class_exists($repositoryClass)) {

                    $groupingProviderClass = $configs['grouping_provider'] ?? null;
                    if (!$groupingProviderClass || !class_exists($groupingProviderClass)) {
                        $groupingProviderClass = '\Zento\Kernel\Booster\Config\ConfigInDB\GroupingProvider';
                    }
                    $this->app['config'] = new Repository($this->app['config'], new $repositoryClass(new $groupingProviderClass));
                }
            }
        }
    }
}