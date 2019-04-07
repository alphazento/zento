<?php
namespace Zento\Kernel\ThemeManager;

class ThemeManagerService {
    protected $viewFinder;
    protected $viewFactory;

    public function __construct($app) {
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
            $packages = \Zento\Kernel\Facades\PackageManager::loadPackagesConfigs();
            $options = [];
            foreach($packages ?? [] as $packageConfig) {
                if ($packageConfig->enabled && $packageConfig->theme) {
                    $options[] = $packageConfig->name;
                }
            }
        }

        return $options;
    }
}