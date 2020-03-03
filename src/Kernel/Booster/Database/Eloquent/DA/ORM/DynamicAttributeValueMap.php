<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DA\ORM;

class DynamicAttributeValueMap extends \Illuminate\Database\Eloquent\Model {
    protected $fillable = ['attribute_id', 'id', 'value', 'swatch_value'];
}
