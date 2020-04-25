<?php

namespace Zento\Kernel\ThemeManager;

use Cookie;
use Zento\Kernel\Facades\PackageManager;
use Zento\Kernel\Consts;

class ThemeManagerService {
    protected $app;
    protected $viewFactory;
    protected $whenThemeLoadCallbacks = [];

    public function __construct($app) {
        $this->app = $app;
    }

    protected function getViewFactory() {
        if (!$this->viewFactory) {
            $this->viewFactory = $this->app['view'];
        }
        return $this->viewFactory;
    }

    public function prependLocation($location) {
        $this->getViewFactory()->getFinder()->prependLocation($location);
    }

    public function addLocation($location) {
        $this->getViewFactory()->addLocation($location);
    }

    public function addNamespace($namespace, $paths) {
        $this->getViewFactory()->addNamespace($namespace, $paths);
    }

    public function getViewPaths() {
        return $this->getViewFactory()->getFinder()->getPaths();
    }

    public function changeViewFactory(\Illuminate\View\Factory $factory) {
        $this->viewFactory = $factory;
    }

    /**
     * get all available themes
     */
    public function availableThemes() {
        static $options;
        if (empty($options)) {
            $packages = PackageManager::loadPackagesConfigs();
            $options = [];
            foreach($packages ?? [] as $packageConfig) {
                if ($packageConfig['enabled'] && $packageConfig['theme']) {
                    $options[] = $packageConfig['name'];
                }
            }
        }

        return $options;
    }

    /**
     * use theme for browser
     */
    public function setTheme($themeType) {
        Cookie::queue('theme', $themeType);
        $packageName = config(sprintf(Consts::CACHE_KEY_THEME_BY, $themeType)) ??  config(Consts::CACHE_KEY_DESKTOP_THEME);
        if (!$this->attachThemePackage($packageName)) {
            throw new \Exception(sprintf('Theme package[%s] not found or not actived.', $packageName));
        }
        return $this;
    }

    public function setThemePackage($packageName) {
        $viewLocation = PackageManager::packageViewsPath($packageName);
        if (!Str::startsWith($viewLocation, $this->basePath)) {
            $viewLocation = base_path($viewLocation);
        }
        if (file_exists($viewLocation)) {
            $this->prependLocation($viewLocation);
        }
        $this->callWhenSetTheme($packageName);
        return $this;
    }

    public function whenSetTheme($themeName, \Closure $callback) {
        $this->whenThemeLoadCallbacks[$themeName] = $callback;
    }

    protected function callWhenSetTheme($themeName) {
        if ($callback = ($this->whenThemeLoadCallbacks[$themeName] ?? false)) {
            $callback($this->app);
        }
    }

    protected function attachThemePackage($packageName) {
        if ($packageConfig = PackageManager::getPackageConfig($packageName)) {
            if ($packageConfig['enabled'] ?? false) {
                if ($assembly = PackageManager::assembly($packageName)) {
                    if ($assembly['theme'] ?? false) {
                        if (is_string($assembly['theme'])) {
                            $this->attachThemePackage($assembly['theme']);
                        }
                        $this->setThemePackage($packageName);
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
