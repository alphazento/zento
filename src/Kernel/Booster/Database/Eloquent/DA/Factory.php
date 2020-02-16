<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DA;

use DB;
use Schema;
use Cache;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Schema\Blueprint;

use Zento\Kernel\Facades\ShareBucket;
use Zento\Kernel\Booster\Database\Eloquent\DA\ORM\DynamicAttributeInSet;
use Zento\Kernel\Booster\Database\Eloquent\DA\ORM\DynamicAttribute;
use Zento\Kernel\Booster\Database\Eloquent\DA\Relationship\Single as SingleRelationship;
use Zento\Kernel\Booster\Database\Eloquent\DA\Relationship\Option as OptionRelationship;

class Factory {

    protected $cache;

    /**
     * @var boolean
     * some dynamc attributes using value map, if set false, will just return id
     */
    protected $_withMappedValue = true;

    public function __construct() {
        $this->cache = [];
    }

    public function withMappedValue() {
        $this->_withMappedValue = true;
        return $this;
    }

    public function withoutMappedValue() {
        $this->_withMappedValue = false;
        return $this;
    }

    public function isWithMappedValue() {
        return $this->_withMappedValue ;
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
        return $this->getDATableName(
            $parent->getTable(),
            $attributeName,
            $single);
    }

    protected function getDATableName(string $parent_table, $attributeName, $single = true) {
        return sprintf('%s_%s_%s', 
            $parent_table,
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
     * @return \Zento\Kernel\Foundation\Eloquent\DA\Builder
     */
    public function withSingleDynamicAttribute(\Illuminate\Database\Eloquent\Builder $builder, $attributeName) {
        return (new Builder($builder))->withSingleDynamicAttribute($attributeName);
    }

    /**
     * add a option relation column to a query builder
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $attributeName
     * @return \Zento\Kernel\Foundation\Eloquent\DA\Builder
     */
    public function withOptionDynamicAttribute(\Illuminate\Database\Eloquent\Builder $builder, $attributeName) {
        return (new Builder($builder))->withOptionDynamicAttribute($attributeName);
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
    public function createRelationShipORM($parentClassOrModel, 
        $attributeName, 
        $valueDes, 
        $single = true, 
        $withValeMap = false, 
        $defaultValue = '') {
        if (is_string($parentClassOrModel) && class_exists($parentClassOrModel)) {
            $parent = new $parentClassOrModel();
        } else {
            $parent = $parentClassOrModel;
        }
        $tableName = $this->getTable($parent, $attributeName, $single);
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) use ($parent, $valueDes, $single) {
                $table->increments('id');
                $driver = new \Zento\Kernel\Booster\Database\Eloquent\DA\Schema\Mysql;
                $driver->addParentKeyColumn($parent, $table, $single);
                $driver->addValueColumn($table, ...$valueDes);
                if (!$single) {
                    $table->integer('sort')->default(0);
                    $table->tinyInteger('disabled')->default(0);
                }
                $table->timestamps();
                $table->foreign('foreignkey')
                    ->references($parent->getKeyname())
                    ->on($parent->getTable());
            });
        }

        // if (config('dynamicattribute_management')) {
            $modelcolumn = DynamicAttribute::where('parent_table', $parent->getTable())
                ->where('attribute_name', $attributeName)
                ->first();
            if (!$modelcolumn) {
                $modelcolumn = new DynamicAttribute();
            }
         
            $modelcolumn->parent_table = $parent->getTable();
            $modelcolumn->attribute_name = $attributeName;
            $modelcolumn->attribute_table = $tableName;
            $modelcolumn->attribute_type = $valueDes[0];
            $modelcolumn->single = $single;
            $modelcolumn->with_value_map = $withValeMap;
            $modelcolumn->default_value = $defaultValue;
            $modelcolumn->save();
            // $cacheKey = $this->getDynamicAttributeCacheKey($parent->getTable());
            // Cache::forget($cacheKey);
            // unset($this->cache[$cacheKey]);
            $this->cache = [];
            return [$modelcolumn->id, $tableName];
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
    public function getDynamicAttributes($modelInstanceOrClass, array &$attrSetIds) {
        $instance = $modelInstanceOrClass instanceof Model ? $modelInstanceOrClass : (new $modelInstanceOrClass);
        $tableName = $instance->getTable();
        $cacheKey = static::getDynamicAttributeCacheKey($tableName, $attrSetIds);
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        // if (Cache::has($cacheKey)) {
        //     return Cache::get($cacheKey);
        // }

        $collection = DynamicAttribute::with('options')
            ->where('parent_table', $tableName)
            ->where('active', 1);
        
        if (count($attrSetIds) > 0) {
            $collection->whereIn('id', DynamicAttributeInSet::whereIn('attribute_set_id', $attrSetIds)
                ->groupBy('attribute_id')
                ->pluck('attribute_id'));
        }
        $collection = $collection->get()
            ->toArray();
         
        // Cache::put($cacheKey, $collection);
        $this->cache[$cacheKey] = $collection;
        return $collection;
    }

    public function getAttributeDesc($tableName) {
        $key = sprintf('%s.desc', $tableName);
        if (ShareBucket::has($key)) {
            return ShareBucket::get($key);
        }

        if (Cache::has($key)) {
            $configs = Cache::get($key);
            ShareBucket::put($key, $configs);
            return $configs;
        }
        
        $configs = [];
        if ($desc = DynamicAttribute::where('attribute_table', $tableName)
                ->first()) {
            $configs = $desc->toArray();
            if ($desc->with_value_map) {
                $rawOptions = $desc->options()->select(['value_id', 'value'])->get();
                $options = [];
                foreach($rawOptions as $option) {
                    $options[$option['value_id']] = $option['value'];
                }
                $configs['options'] = (count($options) > 0 ? $options : false);
            }
        }
        Cache::forever($key, $configs);
        ShareBucket::put($key, $configs);
        return $configs;
    }
}
