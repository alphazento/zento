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
        class_alias('\Zento\Kernel\Facades\DanamicAttributeFactory', 'DanamicAttributeFactory');
    }

    public function boot() {
        // $this->app->register(EventsServiceProvider::class);
        if (!$this->app->runningInConsole()) {
            $configPath = __DIR__ . '/../../../config/zento.php';
            $this->publishes([$configPath => $this->getConfigPath()], 'config');
        }

        foreach ($this->bootedCallbacks as $callback) {
            call_user_func($callback, $this->app);
        }

        // DanamicAttributeFactory::createRelationShipORM(\Zento\Kernel\Booster\Config\ConfigInDB\ORMModel\ConfigItem::class, 
        //     'tcol', ['char', 32], true);
        // // $collection = DanamicAttributeFactory::withDynamicSingleAttribute(\Zento\Kernel\Booster\Config\ConfigInDB\ORMModel\ConfigItem::where('key', 'test'),
        // //     'new_column')->get();
        // DanamicAttributeFactory::createRelationShipORM(\Zento\Kernel\TestModel::class, 
        //     'new_column', ['char', 32], true);
        // DanamicAttributeFactory::createRelationShipORM(\Zento\Kernel\TestModel::class, 
        //     'new_column1', ['char', 32], false);

        // \Zento\Kernel\TestModel::listDynamicAttributes();
        // $collection = \Zento\Kernel\TestModel::where('id', 1)->withDynamicSingleAttribute('new_column')->withDynamicOptionAttribute('new_column1')->first();
        // // $collection = \Zento\Kernel\TestModel::where('id', 2)->withDynamicSingleAttribute('new_column')->first();
        // // echo '<pre>';
        // DanamicAttributeFactory::single($collection, 'new_column')->new('OKOK');
        // DanamicAttributeFactory::option($collection, 'new_column1')->new('this is a test');
        // DanamicAttributeFactory::option($collection, 'new_column1')->setValues(['this is a test', 'newvalue']);

        // $collection = \Zento\Kernel\TestModel::where('id', 1)->withDynamicOptionAttributeet()->first();
        // var_dump($collection->attributesets->attributes);die;
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