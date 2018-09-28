<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute;

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
     * @return Model Dyna Attribute instance
     */
    protected function makeModel($parent = null) {
        $model = $this->_isSingle ? (new ORM\SingleDynamicAttribute()) : (new ORM\OptionDynamicAttribute());
        $model->setConnection(($parent ?? $this->parent)->getConnectionName());
        $model->setTable($this->table);
        return $model;
    }

    /**
     * find a Model instance by parent's key
     *
     * @param Model $parent
     * @return Model Dyna Attribute instance
     */
    protected function findModel($parent = null) {
        $row = DB::connection(($parent ?? $this->parent)->getConnectionName())
            ->table($this->table)
            ->where('foreignkey', ($parent ?? $this->parent)->getKey())
            ->first();
        
        if ($row) {
            $model = $this->makeModel($parent);
            $model->setRawAttributes((array)$row, true);
            $model->exists = true;
            return $model;
        } else {
            return null;
        }
    }

    /**
     * add a new dynamic attribute value
     *
     * @param string $value
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
     * update a dynamic attribute value
     *
     * @param string $value
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
     * delete a dynamic attribute value
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
