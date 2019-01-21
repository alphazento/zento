<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DA\ORM;

class DynamicAttributeSet extends \Illuminate\Database\Eloquent\Model {
    public function attributes() {
        return $this->HasManyThrough(DynamicAttribute::class, 
            Attribute::class,
            'attribute_set_id', 'id', 'id', 'attribute_id');
    }
}