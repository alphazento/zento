<?php

namespace Zento\Kernel\PackageManager\Foundation;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Zento\Kernel\Consts;

class MyPackageManifest extends \Illuminate\Foundation\PackageManifest
{
    protected $app;
    protected $packageNamePathMapping;
    protected $myPackagesFolder;
    /**
     * Create a new package manifest instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $basePath
     * @param  string  $manifestPath
     * @param  Object $app
     * @return void
     */
    public function __construct(\Illuminate\Contracts\Foundation\Application $app)
    {
        $origin = $app[\Illuminate\Foundation\PackageManifest::class];

        parent::__construct(
            $origin->files, 
            $origin->basePath, 
            $app->bootstrapPath().'/cache/zentopackages.php');
        $this->app = $app;
        $this->myPackagesFolder = config('zento.Zento_Kernel.mypackages_folder', 'mypackages');
    }

    /**
     * Build the manifest and write it to disk.
     *
     * @return void
     */
    public function build()
    {
        $this->buildVendorPackages()
            ->buildMyPackages()
            ->write(config('zento'));
        $this->manifest = null;
    }

    protected function rglob($pattern, $flags = 0) {
        $files = glob($pattern, $flags); 
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, $this->rglob($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }

    /**
     * find all vendor packages if contains _zento_assembly.php file
     */
    protected function buildVendorPackages() {
        $files = $this->rglob('vendor/**/' . Consts::PACKAGE_ASSEMBLE_FILE);
        foreach($files as $filename) {
            $this->mergeConfigFromAssemble($filename, 'zento');
        }
        return $this;
    }

    /**
     * load all mypackages
     */
    protected function buildMyPackages() {
        $this->buildPackages($this->myPackagesFolder);
        return $this;
    }

    protected function buildPackages($basePath) {
        $path = base_path($basePath);
        $files = glob(sprintf('%s/**/**/%s', $path, Consts::PACKAGE_ASSEMBLE_FILE));
        foreach($files as $filename) {
            $this->mergeConfigFromAssemble($filename, 'zento');
        }
        return $this;
    }

    protected function mergeConfigFromAssemble($path, $key)
    {
        $moduleConfigs = require $path;
        $module_name = '';
        $configs = null;
        foreach($moduleConfigs as $name => $values) {
            $module_name = $name;
            $configs = $values;
            $configs['module_path'] = rtrim(str_replace(Consts::PACKAGE_ASSEMBLE_FILE, '', $path), '/');
            break;
        }
        if (!$configs) {
            echo $path . ' is not available assemble file' . PHP_EOL;
            return;
        }

        $key = sprintf('%s.%s', $key, $module_name);
        $config = $this->app['config']->get($key, []);
        $this->app['config']->set($key, array_merge($configs, $config));
    }
}
