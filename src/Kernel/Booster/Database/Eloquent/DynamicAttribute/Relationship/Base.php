<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\Relationship;

use DB;
use Illuminate\Database\Eloquent\Model;
use Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM\DynamicSingleAttribute;
use Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM\DynamicOptionAttribute;

class Base {
    protected $model;
    protected $parent;
    
    /**
     * @param Model $parent
     * @param string $model
     */
    public function __construct($parent, $table) {
        $this->parent = $parent;
        $this->table = $table;
    }

    public function isSingle() {
        return true;
    }

    /**
     * create a new Model instance, set connection and table name
     *
     * @param Model $parent
     * @return Model Dyna Attribute instance
     */
    protected function makeModel($parent = null) {
        $model = $this->isSingle() ? (new DynamicSingleAttribute()) : (new DynamicOptionAttribute());
        $model->setConnection(($parent ?? $this->parent)->getConnectionName());
        $model->setTable($this->table);
        return $model;
    }
}
