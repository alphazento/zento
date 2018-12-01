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

class KernelProvider extends \Illuminate\Support\ServiceProvider {
    public function register() {
        $this->app->register(PackageManagerServiceProvider::class);
        $this->app->register(DebuggerServiceProvider::class);
        $this->app->register(EventsServiceProvider::class);
        $this->app->register(DanamicAttributeFactoryProvider::class);
        $this->app->register(ConfigServiceProvider::class);
    }

    /**
     * Get the config path
     *
     * @return string
     */
    protected function getConfigPath()
    {
        return config_path('zento.php');
    }

    public function boot() {
        if ($this->app->runningInConsole()) {
            $configPath = __DIR__ . '/../../../config/zento.php';
            $this->publishes([$configPath => $this->getConfigPath()], 'Zento');
            Schema::defaultStringLength(191);
        }
    }
}