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

use Zento\Kernel\Facades\DynaColumnFactory;
use DB;
class KernelProvider extends \Illuminate\Support\ServiceProvider {
    public function register() {
        $this->app->register(DebuggerServiceProvider::class);
        $this->app->register(LogServiceProvider::class);
        $this->app->register(EventsServiceProvider::class);
        $this->app->register(DynaColumnFactoryProvider::class);
        $this->app->register(PackageManagerServiceProvider::class);
        $this->app->register(ConfigServiceProvider::class);
    }

    public function boot() {
        $configPath = __DIR__ . '/../../../config/zento.php';
        $this->publishes([$configPath => $this->getConfigPath()], 'config');

        DynaColumnFactory::createRelationShipORM(\Zento\Kernel\Booster\Config\ConfigInDB\ORMModel\ConfigItem::class, 
            'tcol', ['char', 32], true);
        // $collection = DynaColumnFactory::withDynaColumn(\Zento\Kernel\Booster\Config\ConfigInDB\ORMModel\ConfigItem::where('key', 'test'),
        //     'new_column')->get();
        DynaColumnFactory::createRelationShipORM(\Zento\Kernel\TestModel::class, 
            'new_column', ['char', 32], true);
            DynaColumnFactory::createRelationShipORM(\Zento\Kernel\TestModel::class, 
            'new_column1', ['char', 32], false);
            \Zento\Kernel\TestModel::listDynaColumns();
        $collection = \Zento\Kernel\TestModel::where('id', 1)->withDyn('new_column')->first();
        echo '<pre>';

        var_dump($collection->new_column);die;
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
}