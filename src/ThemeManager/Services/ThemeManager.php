<?php
namespace Zento\ThemeManager\Services;

class ThemeManager {
    protected $viewFinder;
    protected $viewFactory;

    public function __construct($app) {
        $this->viewFactory = $app['view'];
        if (self::debugMode()) {
            $this->viewFactory->composer('*', function($view){
                $view_name = str_replace('.', '-', $view->getName());
                view()->share('view_name', $view_name);  //pass view_name to viewDebug
            });
        }
        $this->viewFinder = $this->viewFactory->getFinder();
    }

    public function appendMaker($maker) {
        if (method_exists($this->viewFactory, 'appendMaker')) {
            $this->viewFactory->appendMaker($maker);
        }
    }

    public function prependLocation($location) {
        $this->viewFinder->prependLocation($location);
    }

    /**
     * check if user has cookie 'theme', so we using the user's theme
     */
    public function prependUserThemeLocation($theme) {
        $this->viewFinder->prependLocation(implode('/', [storage_path('framework/userthemes'), $theme]));
    }

    /**
     * get all available themes
     */
    public function availableThemes() {
        static $options;
        if (empty($options)) {
            $packages = PackageManager::getActivePackageConfigs();
            $options = [];
            foreach($packages ?? [] as $packageConfig) {
                if ($packageConfig->is_theme) {
                    $options[] = $packageConfig->name;
                }
            }
        }

        return $options;
    }

    public static function debugMode() {
        return false;
    }
}