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

use Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute\Factory;

class DanamicAttributeFactoryProvider extends \Illuminate\Support\ServiceProvider {
    public function register() {
        $this->app->singleton('DanamicAttributeFactory', function ($app) {
            return new Factory();
        });
    }
}
