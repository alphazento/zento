<?php

namespace Zento\Kernel\ThemeManager;

use Cookie;
use Zento\Kernel\Facades\PackageManager;

class ThemeManagerService {
    protected $app;
    protected $viewFinder;
    protected $viewFactory;
    protected $whenThemeLoadCallbacks = [];

    public function __construct($app) {
        $this->app = $app;
        $this->viewFactory = $app['view'];
        $this->viewFinder = $this->viewFactory->getFinder();
    }

    public function prependLocation($location) {
        $this->viewFinder->prependLocation($location);
    }

    public function addLocation($location) {
        $this->viewFactory->addLocation($location);
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
        if (config('app.theme.enable_cookie', false)) {
            Cookie::queue('theme', $themeType);
        }

        $packageName = config(sprintf('app.theme.%s', $themeType)) ?? config('app.theme.desktop');
        if (!$this->attachThemePackage($packageName)) {
            throw new \Exception(sprintf('Theme package[%s] not found or not actived.', $packageName));
        }
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
                    if ($assembly['theme']) {
                        if (is_string($assembly['theme'])) {
                            $this->attachThemePackage($assembly['theme']);
                        }
                        $viewLocation = PackageManager::packageViewsPath($packageName);
                        if (file_exists($viewLocation)) {
                            $this->viewFinder->prependLocation($viewLocation);
                        }
                        $this->callWhenSetTheme($packageName);
                        return true;
                    }
                }
            }
        }
        return false;
    }
}