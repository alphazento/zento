<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicColumn\ORM;

class SingleDynaColumn extends \Illuminate\Database\Eloquent\Model {
    public function isSingle() {
        return false;
    }
}
