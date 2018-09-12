<?php
namespace Zento\Kernel\PackageManager\Model\ORM;

class PackageConfig extends \Illuminate\Database\Eloquent\Model {
    protected $primaryKey = 'id';

    public static function __callStatic($method, $parameters) {
        $instance = new static;
        return call_user_func_array([$instance, $method], $parameters);
    }

    public function __sleep() {
        return ['exists', 'attributes'];
    }
}
