<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\ORM;

class DynamicSingleAttribute extends \Illuminate\Database\Eloquent\Model {
    public function isSingle() {
        return true;
    }

    public function __toString() {
        return $this->value;
    }

    public function toArray() {
        return $this->value;
    }
}
