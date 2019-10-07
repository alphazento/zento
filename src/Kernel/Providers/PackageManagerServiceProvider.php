<?php
/**
 *
 * @category   Framework support
 * @package    Zento
 * @copyright
 * @license
 * @author      Yongcheng Chen yongcheng.chen@live.com
 */

namespace Zento\Kernel\Providers;

use Zento\Kernel\PackageManager\PackageManagerService;
use Zento\Kernel\PackageManager\Console\Foundation\ArtisanSubscriber;

class PackageManagerServiceProvider extends \Illuminate\Support\ServiceProvider {
    public function register() {
        $this->app->singleton('packagemanager', function ($app) {
            return new PackageManagerService($app);
        });
    }

    public function boot() {
        if ($packageManager = $this->app['packagemanager']) {
            if (!$this->app->environment('production')) {
                $packageManager->rebuildPackages();
            }
            $packageManager->inject($this)->mapRoutes();
            $this->app->runningInConsole() && (new ArtisanSubscriber())->subscribe();
        }
    }
    
    /**
     * expose publishes to other provider
     * define for the command [vender:publish] using  (Only for console)
     */
    public function preparePublishes(array $paths, $group = null) {
        $this->publishes($paths, $group);
    }
}
