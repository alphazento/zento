<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicColumn\ORM;

class DynacolumnSet extends \Illuminate\Database\Eloquent\Model {
    public function dynacolumns() {
        return $this->HasManyThrough(ModelDynacolumn::class, 
            DynacolumnSetDynacolumn::class,
            'dynacolumn_set_id', 'id', 'id', 'dynacolumn_id');
    }
}