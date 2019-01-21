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

use Zento\Kernel\Facades\PackageManager;
use Zento\Kernel\Booster\Database\Eloquent\DA\Factory;

class DanamicAttributeFactoryProvider extends \Illuminate\Support\ServiceProvider {
    public function register() {
        $this->app->singleton('danamic_attribute_factory', function ($app) {
            return new Factory();
        });
        PackageManager::class_alias('\Zento\Kernel\Facades\DanamicAttributeFactory', 'DanamicAttributeFactory');
    }

    public function provides()
    {
        return ['danamic_attribute_factory'];
    }
}
