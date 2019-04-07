<?php

namespace Zento\Kernel\Providers;

use Zento\Kernel\Facades\PackageManager;

class ThemeManagerServiceProvider extends \Illuminate\Support\ServiceProvider {
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return ['theme.manager'];
    }

    public function register() {
        $this->app->singleton('theme_manager', function ($app) {
            return new \Zento\Kernel\ThemeManager\ThemeManagerService($app);
        });
        PackageManager::class_alias('\Zento\Kernel\Facades\ThemeManager', 'ThemeManager');
    }
}
