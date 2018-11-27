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

use Zento\Kernel\Facades\DanamicAttributeFactory;
use DB;
class KernelProvider extends \Illuminate\Support\ServiceProvider {
    protected $bootedCallbacks = [];

    public function register() {
        $this->app->singleton('ZentoKernel', function($app) { return $this; });
        $this->app->register(DebuggerServiceProvider::class);
        $this->app->register(EventsServiceProvider::class);
        $this->app->register(DanamicAttributeFactoryProvider::class);
        $this->app->register(PackageManagerServiceProvider::class);
        $this->app->register(ConfigServiceProvider::class);
        $this->app->alias('\Zento\Kernel\Facades\DanamicAttributeFactory', 'DanamicAttributeFactory');
    }

    public function boot() {
        if (!$this->app->runningInConsole()) {
            $configPath = __DIR__ . '/../../../config/zento.php';
            $this->publishes([$configPath => $this->getConfigPath()], 'config');
        }

        foreach ($this->bootedCallbacks as $callback) {
            call_user_func($callback, $this->app);
        }
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

    static function getTableColumnType($tableName, $field, $fullType = false){
        if ($collection = DB::select(sprintf("SHOW COLUMNS FROM %s where field = '%s'", $tableName, $field))){
            if ($field = $collection[0] ?? null) {
                $type = ($fullType || !str_contains($field->Type, '('))? $field->Type: substr($field->Type, 0, strpos($field->Type, '('));
                return [$field->Field, $type];
            }
        }
    }

    public function registerBoot(\Closure $func) {
        $this->bootedCallbacks[] = $func;
    }
}