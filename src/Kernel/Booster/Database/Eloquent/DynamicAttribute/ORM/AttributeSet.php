<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM;

class AttributeSet extends \Illuminate\Database\Eloquent\Model {
    public function attributes() {
        return $this->HasManyThrough(ModelDynamicAttribute::class, 
            Attribute::class,
            'attribute_set_id', 'id', 'id', 'attribute_id');
    }
}