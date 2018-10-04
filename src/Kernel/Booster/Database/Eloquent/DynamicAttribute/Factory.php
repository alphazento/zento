<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute;

use DB;
use Schema;
use Cache;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Schema\Blueprint;

use Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM\AttributeInSet;
use Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM\ModelDynamicAttribute;
use Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\Relationship\Single as SingleRelationship;
use Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\Relationship\Option as OptionRelationship;

class Factory {

    protected $cache;

    public function __construct() {
        $this->cache = [];
    }

    /**
     * get dynamic Attribute's table's name
     *
     * @param Model $hostInstance
     * @param string $attributeName
     * @param boolean $single
     * @return string
     */
    public function getTable(Model $parent, $attributeName, $single = true) {
        return sprintf('%s_%s_%s', 
            $parent->getTable(),
            $single ? 'dyn' : 'dyns',
            Str::plural($attributeName));
    }

    /**
     * get multiple relation ship
     *
     * @param Model $parent
     * @param string $attributeName
     * @return Relationship
     */
    public function option(Model $parent, $attributeName) {
        return $this->retrieveRelationship($parent, $attributeName, false);
    }

    /**
     * get single relation ship
     *
     * @param Model $hostInstance
     * @param string $attributeName
     * @return Relationship
     */
    public function single(Model $parent, $attributeName) {
        return $this->retrieveRelationship($parent, $attributeName, true);
    }

    /**
     * add a single relation dynamic Attribute to a query builder
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $attributeName
     * @return \Zento\Kernel\Foundation\Eloquent\DynamicAttribute\Builder
     */
    public function withDynamicSingleAttribute(\Illuminate\Database\Eloquent\Builder $builder, $attributeName) {
        return (new Builder($builder))->withDynamicSingleAttribute($attributeName);
    }

    /**
     * add a option relation column to a query builder
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $attributeName
     * @return \Zento\Kernel\Foundation\Eloquent\DynamicAttribute\Builder
     */
    public function withDynamicOptionAttribute(\Illuminate\Database\Eloquent\Builder $builder, $attributeName) {
        return (new Builder($builder))->withDynamicOptionAttribute($attributeName);
    }

    /**
     * retrieve relation
     *
     * @param Model $hostInstance
     * @param string $attributeName
     * @param string $single
     * @return void
     */
    protected function retrieveRelationship(Model $parent, $attributeName, $single) {
        $table = $this->getTable($parent, $attributeName, $single);
        return  ($single ? (new SingleRelationship($parent, $table)) : (new OptionRelationship($parent, $table)));
    }

    /**
     * create new dynamic attribute table
     *
     * @param string|\Illuminate\Database\Eloquent\Model $parentClass
     * @param string $attributeName
     * @param array $valueDes  [type, otherParameters], e.g. ['double', 5, 2]
     * @param boolean $single
     * @return string $id
     */
    public function createRelationShipORM($parentClassOrModel, $attributeName, $valueDes, $single = true, $defaultValue = '') {
        if (is_string($parentClassOrModel) && class_exists($parentClassOrModel)) {
            $parent = new $parentClassOrModel();
        } else {
            $parent = $parentClassOrModel;
        }
        $tableName = $this->getTable($parent, $attributeName, $single);
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) use ($parent, $valueDes, $single) {
                $table->increments('id');
                $driver = new \Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\Schema\Mysql;
                $driver->addParentKeyColumn($parent, $table, $single);
                $driver->addValueColumn($table, ...$valueDes);
                if (!$single) {
                    $table->integer('sort')->default(0);
                }
                $table->timestamps();
                $table->foreign('foreignkey')
                    ->references($parent->getKeyname())
                    ->on($parent->getTable());
            });
        }

            // if (config('dynamicattribute_management')) {
                $modelcolumn = ModelDynamicAttribute::firstOrNew([
                    'model' => $parent->getTable(),
                    'attribute' => $attributeName,
                    'attribute_type' => $valueDes[0],
                    'default_value' => $defaultValue
                ]);
                $modelcolumn->single = $single;
                $modelcolumn->save();
                // $cacheKey = $this->getDynamicAttributeCacheKey($parent->getTable());
                // Cache::forget($cacheKey);
                // unset($this->cache[$cacheKey]);
                $this->cache = [];
                return $modelcolumn->id;
            // }
            // return 0;
    }

    protected function getDynamicAttributeCacheKey($tableName, &$attrSetIds) {
        return sprintf('dyn.attr.%s.%s', $tableName, md5(implode('', $attrSetIds)));
    }

    /**
     * get a model's all dynamic attributes desc
     *
     * @param mixed $modelInstanceOrClass
     * @param array $attribute set ids
     * @return array
     */
    public function getModelDynamicAttributes($modelInstanceOrClass, array &$attrSetIds) {
        $instance = $modelInstanceOrClass instanceof Model ? $modelInstanceOrClass : (new $modelInstanceOrClass);
        $tableName = $instance->getTable();
        $cacheKey = static::getDynamicAttributeCacheKey($tableName, $attrSetIds);
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        // if (Cache::has($cacheKey)) {
        //     return Cache::get($cacheKey);
        // }

        $collection = ModelDynamicAttribute::where('model', $tableName);
            // ->where('is_active', 1)
        
        if (count($attrSetIds) > 0) {
            $collection->whereIn('id', AttributeInSet::whereIn('attribute_set_id', $attrSetIds)->groupBy('attribute_id')->pluck('attribute_id'));
        }
        $collection = $collection->get()
            ->toArray();
         
        // Cache::put($cacheKey, $collection);
        $this->cache[$cacheKey] = $collection;
        return $collection;
    }
}
