<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM;

class AttributeValueMap extends \Illuminate\Database\Eloquent\Model {
    protected $fillable = ['attribute', 'value_id', 'value'];
}
