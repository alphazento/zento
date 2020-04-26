<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DA\ORM;

class DynamicAttributeValueMap extends \Illuminate\Database\Eloquent\Model {
    protected $fillable = ['id', 'attribute_id', 'value', 'swatch_value'];
}
