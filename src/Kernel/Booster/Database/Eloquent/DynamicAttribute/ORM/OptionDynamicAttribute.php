<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM;

class OptionDynamicAttribute extends \Illuminate\Database\Eloquent\Model {
    public function isSingle() {
        return false;
    }
}