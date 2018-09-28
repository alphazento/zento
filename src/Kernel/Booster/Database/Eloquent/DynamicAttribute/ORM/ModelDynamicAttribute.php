<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM;

class ModelDynamicAttribute extends \Illuminate\Database\Eloquent\Model {
    protected $fillable = ['model', 'attribute', 'attribute_type', 'default_value'];
}
