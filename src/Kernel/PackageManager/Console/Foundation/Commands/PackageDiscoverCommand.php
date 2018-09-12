<?php

namespace Zento\Kernel\PackageManager\Console\Foundation\Commands;

use Artisan;
use Illuminate\Foundation\PackageManifest;
use Zento\Kernel\PackageManager\Foundation\MyPackageManifest;
use Zento\Kernel\Facades\PackageManager;
use Log;

class PackageDiscoverCommand extends \Illuminate\Foundation\Console\PackageDiscoverCommand 
{
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(PackageManifest $manifest)
    {
        parent::handle($manifest);

        $app = $this->getLaravel();
        if ($app instanceof \Zento\Kernel\PackageManager\Console\Foundation\KernelCommandDispatcher) {
            $app = $app->getApp();
        }

        $this->discoverPackages();
    }

    /**
     * discover all packages(including zento and mypackages)
     *
     * @return void
     */
    protected function discoverPackages() {
        $allPackageConfigs = PackageManager::rebuildPackages()->assemblies();
        $enabledPackageConfigs = PackageManager::loadPackagesConfigs();
        foreach($enabledPackageConfigs ?? [] as $packageConfig) {
            $this->info(sprintf('[%s] version=[%s] actived at %s.', $packageConfig->name, $packageConfig->version, $packageConfig->updated_at));
            if (isset($allPackageConfigs[$packageConfig->name])) {
                unset($allPackageConfigs[$packageConfig->name]);
            }
        }
        foreach($allPackageConfigs as $name => $v) {
            $this->warn(sprintf('[%s] not actived.', $name));
        }
    }
}
