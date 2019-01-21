<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM;

class ModelDynamicAttribute extends \Illuminate\Database\Eloquent\Model {
    protected $fillable = ['model', 'attribute', 'attribute_type', 'default_value'];

    public function options() {
        return $this->hasMany(AttributeValueMap::class, 'attribute_id', 'id');
    }
}
