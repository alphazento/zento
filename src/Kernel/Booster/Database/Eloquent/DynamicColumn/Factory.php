<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicColumn;

use DB;
use Schema;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Schema\Blueprint;

class Factory {

    protected $cache;

    public function __construct() {
        $this->cache = [];
    }

    /**
     * get dynamic column's table's name
     *
     * @param Model $hostInstance
     * @param string $columnName
     * @param boolean $single
     * @return string
     */
    public function getTable(Model $parent, $columnName, $single = true) {
        return sprintf('%s_%s_%s', 
            $parent->getTable(),
            $single ? 'dyn' : 'dyns',
            Str::plural($columnName));
    }

    /**
     * get multiple relation ship
     *
     * @param Model $parent
     * @param string $columnName
     * @return Relationship
     */
    public function option(Model $parent, $columnName) {
        return $this->retrieveRelationship($parent, $columnName, false);
    }

    /**
     * get single relation ship
     *
     * @param Model $hostInstance
     * @param string $columnName
     * @return Relationship
     */
    public function single(Model $parent, $columnName) {
        return $this->retrieveRelationship($parent, $columnName, true);
    }

    /**
     * add a single relation dynamic column to a query builder
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $columnName
     * @return \Zento\Kernel\Foundation\Eloquent\DynamicColumn\Builder
     */
    public function withDynaColumn(\Illuminate\Database\Eloquent\Builder $builder, $columnName) {
        return (new Builder($builder))->withDynaColumn($columnName);
    }

    /**
     * add a option relation column to a query builder
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $columnName
     * @return \Zento\Kernel\Foundation\Eloquent\DynamicColumn\Builder
     */
    public function withDynaColumns(\Illuminate\Database\Eloquent\Builder $builder, $columnName) {
        return (new Builder($builder))->withDynaColumns($columnName);
    }

    /**
     * retrieve relation
     *
     * @param Model $hostInstance
     * @param string $columnName
     * @param string $single
     * @return void
     */
    protected function retrieveRelationship(Model $parent, $columnName, $single) {
        $table = $this->getTable($parent, $columnName, $single);
        if (!isset($this->cache[$table])) {
            $this->cache[$table] = new Relationship($parent, $columnName, $single);
        }
        return $this->cache[$table];
    }

    /**
     * create new dynacolumn table
     *
     * @param string|\Illuminate\Database\Eloquent\Model $parentClass
     * @param string $columnName
     * @param array $valueDes  [type, otherParameters], e.g. ['double', 5, 2]
     * @param boolean $single
     * @return void
     */
    public function createRelationShipORM($parentClassOrModel, $columnName, $valueDes, $single = true) {
        if (is_string($parentClassOrModel) && class_exists($parentClassOrModel)) {
            $parent = new $parentClassOrModel();
        } else {
            $parent = $parentClassOrModel;
        }
        $tableName = $this->getTable($parent, $columnName);
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) use ($parent, $valueDes, $single) {
                $table->increments('id');
                $driver = new \Zento\Kernel\Booster\Database\Eloquent\DynamicColumn\Schema\Mysql;
                $driver->addParentKeyColumn($parent, $table, $single);
                $driver->addValueColumne($table, ...$valueDes);
                if (!$single) {
                    $table->integer('sort')->default(0);
                }
                $table->timestamps();
                $table->foreign('foreignkey')
                    ->references($parent->getKeyname())
                    ->on($parent->getTable());
            });
        }
    }
}
