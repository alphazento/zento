<?php
namespace Zento\Kernel;

class TestModel extends \Illuminate\Database\Eloquent\Model {
    use \Zento\Kernel\Booster\Database\Eloquent\DynamicColumn\DynamicColumnAbility;
    protected $table = 'package_configs';

    // public function new_column() {
    //     return $this->hasOne(TestModel1::class, 'foreignkey');
    // }
}
