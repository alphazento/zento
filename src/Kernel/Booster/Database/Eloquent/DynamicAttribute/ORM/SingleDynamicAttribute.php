<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM;

class SingleDynamicAttribute extends \Illuminate\Database\Eloquent\Model {
    public function isSingle() {
        return false;
    }
}
