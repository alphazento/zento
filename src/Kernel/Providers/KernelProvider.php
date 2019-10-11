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

use DB;
use Illuminate\Support\Facades\Schema;
use Zento\Kernel\Support\ShareBucket;
use Zento\Kernel\Support\InnerApiClient;
use Zento\Kernel\Facades\PackageManager;

class KernelProvider extends \Illuminate\Support\ServiceProvider {
    public function register() {
        $this->mixins();

        $this->app->register(PackageManagerServiceProvider::class);
        $this->app->register(ThemeManagerServiceProvider::class);
        $this->app->register(DebuggerServiceProvider::class);
        $this->app->register(EventsServiceProvider::class);
        $this->app->register(DanamicAttributeFactoryProvider::class);
        $this->app->register(ConfigServiceProvider::class);

        $this->app->singleton('sharebucket', function ($app) {
            return new ShareBucket();
        });
        $this->app->singleton('innerapiclient', function ($app) {
            return new InnerApiClient();
        });
        PackageManager::class_alias('\Zento\Kernel\Facades\ShareBucket', 'ShareBucket');
        PackageManager::class_alias('\Zento\Kernel\Facades\InnerApiClient', 'InnerApiClient');
    }

    /**
     * Get the config path
     *
     * @return string
     */
    protected function getConfigPath()
    {
        return config_path('zento/Zento_Kernel.php');
    }

    public function boot() {
        if ($this->app->runningInConsole()) {
            $configPath = __DIR__ . '/../config/Zento_Kernel.php';
            $this->publishes([$configPath => $this->getConfigPath()], 'Zento');
            Schema::defaultStringLength(191);
        }
    }

    protected function mixins() {
        \Illuminate\Routing\Route::mixin(new \Zento\Kernel\Booster\Mixins\Routing\Route);
        \Illuminate\Routing\UrlGenerator::mixin(new \Zento\Kernel\Booster\Mixins\Routing\UrlGenerator);
        // app('url')->setAssetRoot();
    }
}