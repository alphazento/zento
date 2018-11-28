<?php
/**
 *
 * @category   Framework support
 * @package    Zento
 * @copyright
 * @license
 * @author      Yongcheng Chen yongcheng.chen@live.com
 */

namespace Zento\Kernel\PackageManager\Console\Commands;

use Artisan;
use Zento\Kernel\Facades\PackageManager;

class DisablePackage extends Base
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:disable
      {name : package name}';

    protected $description = "Disable package.(but it's classes still can be used.";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $packageName = $this->argument('name');

        $packageConfig = PackageManager::findPackageConfigOrNew($packageName);
        if ($packageConfig) {
            PackageManager::down($packageName);
            $this->info(sprintf('Package [%s] has been disabled.', $packageName));
        } else {
            $this->error(sprintf("Package [%s] not exist or haven't be enabled.", $packageName));
        }
        $this->call('route:cache');
    }
}
