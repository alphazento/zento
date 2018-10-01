<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM;

class DynamicOptionAttribute extends \Illuminate\Database\Eloquent\Model {
    public function isSingle() {
        return false;
    }

    public function __toString() {
        return $this->value;
    }
}