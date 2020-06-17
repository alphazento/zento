<?php
/**
 *
 * @category   Framework support
 * @package    Zento
 * @copyright
 * @license
 * @author      Yongcheng Chen yongcheng.chen@live.com
 */

namespace Zento\Kernel\Booster\Config\Console\Commands;

use StoreConfig;

class SetConfig extends \Zento\Kernel\PackageManager\Console\Commands\Base
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:set {key : key} {value : value}';

    protected $description = "set config value";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $key = $this->argument('key');
        $value = $this->argument('value');
        StoreConfig::save($key, $value);
        $this->info(sprintf('config pair added.  %s=%s', $key, config($key)));
    }
}
