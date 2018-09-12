<?php
/**
 *
 * @category   Framework support
 * @package    Zento
 * @copyright
 * @license
 * @author      Yongcheng Chen yongcheng.chen@live.com
 */

namespace Zento\Kernel\Providers;

use Zento\Kernel\Booster\Database\Eloquent\DynamicColumn\Factory;

class DynaColumnFactoryProvider extends \Illuminate\Support\ServiceProvider {
    public function register() {
        $this->app->singleton('DynaColumnFactory', function ($app) {
            return new Factory();
        });
    }
}
