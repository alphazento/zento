<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM;

class DynamicOptionAttribute extends \Illuminate\Database\Eloquent\Model {
    protected $attr_name;

    public function setAttrName($attr_name) {
        $this->attr_name = $attr_name; 
        return $this;
    }

    public function isSingle() {
        return false;
    }

    public function __toString() {
        return $this->value;
    }

    public function toArray() {
        return $this->value;
    }

    /**
     * Create a new instance of the model being queried.
     *
     * @param  array  $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function newModelInstance($attributes = [])
    {
        return parent::newModelInstance($attributes)->setAttrName($this->attr_name);
    }

    public function newFromBuilder($attributes = [], $connection = null) {
        dd($attributes);
    }
}