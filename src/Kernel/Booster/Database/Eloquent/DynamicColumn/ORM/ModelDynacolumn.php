<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicColumn\ORM;

class ModelDynacolumn extends \Illuminate\Database\Eloquent\Model {
    protected $fillable = ['model', 'dynacolumn', 'col_type', 'default_value'];
}
