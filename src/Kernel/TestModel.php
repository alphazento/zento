<?php
namespace Zento\Kernel;

class TestModel extends \Illuminate\Database\Eloquent\Model {
    use \Zento\Kernel\Booster\Database\Eloquent\DynamicColumn\DynamicColumnAbility;
    // protected $table = 'package_configs';

    // public function new_column() {
    //     return $this->hasOne(TestModel1::class, 'foreignkey');
    // }

    // CREATE TABLE `test_models` (
    //     `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    //     `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    //     `dynacolumn_set_id` int(10) unsigned NOT NULL,
    //     PRIMARY KEY (`id`)
    //   );
}
