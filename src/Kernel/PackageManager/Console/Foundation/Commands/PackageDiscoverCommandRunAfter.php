<?php

namespace Zento\Kernel\PackageManager\Console\Foundation\Commands;

use Artisan;
// use Illuminate\Foundation\PackageManifest;
// use Zento\Kernel\PackageManager\Foundation\MyPackageManifest;
use Zento\Kernel\Facades\PackageManager;

class PackageDiscoverCommandRunAfter
{
    protected $_stdout;
    public function __construct($input, $output) {
        $this->_stdout = new \Illuminate\Console\OutputStyle(
            $input, $output
        );
    }
    /**
     * discover all packages(including zento and mypackages)
     *
     * @return void
     */
    public function discoverMyPackages() {
        $allPackageConfigs = PackageManager::rebuildPackages()->assemblies();
        $enabledPackageConfigs = PackageManager::loadPackagesConfigs(true);
        foreach($enabledPackageConfigs ?? [] as $name => $packageConfig) {
            if (isset($allPackageConfigs[$name])) {
                $this->_stdout->success(sprintf('[%s][%s] version=[%s] actived at %s.', 
                    $packageConfig['sort'], 
                    $name, 
                    $packageConfig['version'], 
                    $packageConfig['updated_at']));
                if (isset($allPackageConfigs[$name])) {
                    unset($allPackageConfigs[$name]);
                }
            } else {
                $this->_stdout->warning(sprintf('[%s] source code is no longer exists.', $name)); 
                PackageManager::deletePackageConfig($packageConfig['id']);
            }
        }
        foreach($allPackageConfigs as $name => $v) {
            $this->_stdout->warning(sprintf('[%s] not actived.', $name));
        }
    }
}
