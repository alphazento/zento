<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DA\Relationship;

use DB;
use Illuminate\Database\Eloquent\Model;
use Zento\Kernel\Booster\Database\Eloquent\DA\ORM\DynamicAttribute\Single as SingleDynamicAttribute;
use Zento\Kernel\Booster\Database\Eloquent\DA\ORM\DynamicAttribute\Option as OptionDynamicAttribute;

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
    protected function makeModel() {
        $this->model = $this->isSingle() ? (new SingleDynamicAttribute()) : (new OptionDynamicAttribute());
        $this->model->setConnection($this->parent->getConnectionName());
        $this->model->setTable($this->table);
        return $this->model;
    }
}
