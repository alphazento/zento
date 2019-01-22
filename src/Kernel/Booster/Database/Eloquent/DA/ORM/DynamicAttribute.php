<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DA\ORM;

class DynamicAttribute extends \Illuminate\Database\Eloquent\Model {
    protected $fillable = ['parent_table', 'attribute_name', 'attribute_table', 'attribute_type', 'default_value'];

    public function options() {
        return $this->hasMany(DynamicAttributeValueMap::class, 'attribute_id', 'id');
    }
}
