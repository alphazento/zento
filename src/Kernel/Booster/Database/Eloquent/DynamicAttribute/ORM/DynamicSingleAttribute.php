<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM;

class DynamicSingleAttribute extends \Illuminate\Database\Eloquent\Model {
    public function isSingle() {
        return false;
    }
}
