<?php

namespace Zento\ThemeManager\Providers;

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
        if (!$this->app->runningInConsole()) {
            $this->registerViewFactory();
            $this->app->singleton('theme.manager', function ($app) {
                return new \Zento\ThemeManager\Services\ThemeManager($app);
            });
            $this->app->alias('\Zento\ThemeManager\Providers\Facades\ThemeManager', 'ThemeManager');
        }
    }

    public function boot() {
        if (!$this->app->runningInConsole()) {
            PackageManager::booted(function($app) {
                $packageConfigs = PackageManager::loadPackagesConfigs();
                foreach($packageConfigs as $packageConfig) {
                    $viewLocation = PackageManager::packageViewsPath($packageConfig->name);
                    if (file_exists($viewLocation)) {
                        if ($packageConfig->is_theme) {
                            //register package's view locations, only for not theme package.
                            ThemeManager::prependlocation($viewLocation);
                       } else {
                           $app['view']->addLocation($viewLocation);                
                       }
                    }
                }
                if (\Zento\ThemeManager\Services\ThemeManager::debugMode()) {
                    (new \Zento\ThemeManager\View\Debug\BladeExtender())->inject($app);
                }
            });
        }
    }

    /**
     * replace finder with Extend view finder
     */
    protected function registerViewFactory() {
        $this->app->singleton('view', function($app) {
            $resolver = $app['view.engine.resolver'];
            $finder = $app['view.finder'];

            $finder = new \Zento\ThemeManager\View\Finders\Manager($finder, $app);
            $app['url'] = new \Zento\ThemeManager\Override\Illuminate\Routing\UrlGenerator($app['url']);

            $factory = new \Zento\ThemeManager\View\ViewFactory($resolver, $finder, $app['events']);
            $factory->setContainer($app);
            $factory->share('app', $app);
            return $factory;
        });
    }
}
