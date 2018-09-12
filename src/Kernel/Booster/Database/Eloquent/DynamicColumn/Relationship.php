<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicColumn;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as OriginBuilder;

class Relationship {
    protected $_isSingle = true;
    protected $model;
    protected $parent;
    
    /**
     * Undocumented function
     *
     * @param Model $parent
     * @param string $model
     * @param boolean $isSingle
     */
    public function __construct($parent, $table, $isSingle) {
        $this->parent = $parent;
        $this->table = $table;
        $this->_isSingle = $isSingle;
    }

    public function isSingle() {
        return $this->_isSingle;
    }

    /**
     * create a new Model instance, set connection and table name
     *
     * @param Model $parent
     * @return Model Dyna Column instance
     */
    protected function makeModel($parent = null) {
        $model = $this->_isSingle ? (new ORM\SingleDynaColumn()) : (new ORM\OptionDynaColumn());
        $model->setConnection(($parent ?? $this->parent)->getConnectionName());
        $model->setTable($this->table);
        return $model;
    }

    /**
     * find a Model instance by parent's key
     *
     * @param Model $parent
     * @return Model Dyna Column instance
     */
    protected function findModel($parent = null) {
        return DB::connection(($parent ?? $this->parent)->getConnectionName())
            ->table($this->table)
            ->where('foreignkey', ($parent ?? $this->parent)->getKey())
            ->first();
    }

    /**
     * add a new dynacolumn value
     *
     * @param string $columnName
     * @param Model $parent
     * @return Model
     */
    public function new($value, $parent = null) {
        $model = $this->makeModel();
        $model->foreignkey = ($parent ?? $this->parent)->getKey();
        $model->value = $value;
        $model->save();
    }

    /**
     * update a dynacolumn value
     *
     * @param string $columnName
     * @param Model $parent
     * @return void
     */
    public function update($value, $parent = null) {
        $model = $this->findModel($parent) ?? $this->makeModel();
        $model->foreignkey = ($parent ?? $this->parent)->getKey();
        $model->value = $value;
        $model->save();
    }

    /**
     * delete a dynacolumn value
     *
     * @param Model $parent
     * @return void
     */
    public function delete($parent = null) {
        if ($model = $this->findModel($parent)) {
            $model->delete();
        }
    }

    public function getValue($parent = null) {
        if ($model = $this->findModel($parent)) {
            return $model->value;
        }
        return null;
    }
}
