<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DA\ORM\DynamicAttribute;

class Option extends Single {
    public function isSingle() {
        return false;
    }
}