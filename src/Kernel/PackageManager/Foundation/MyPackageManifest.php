<?php

namespace Zento\Kernel\PackageManager\Foundation;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Zento\Kernel\Consts;

class MyPackageManifest extends \Illuminate\Foundation\PackageManifest
{
    protected $app;
    protected $packageNamePathMapping;
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
    }


    /**
     * Build the manifest and write it to disk.
     *
     * @return void
     */
    public function build()
    {
        $packages = [];
        $this->preparePackageNamePathMapping()
            ->buildZentoPackages($packages)
            ->buildMyPackages($packages)
            ->write($packages);
        $this->manifest = null;
    }

    /**
     * build all zento packages
     *
     * @param array $packages
     * @return void
     */
    protected function buildZentoPackages(array &$packages) {
        $manifest = $this->app[\Illuminate\Foundation\PackageManifest::class]->manifest ?? [];
        foreach($manifest as $compName => $configs) {
            foreach($configs['zento'] ?? [] as $packageName => $assembly) {
                $packages[$packageName] = [
                    'path' => $this->getZentoPackageRealPath($packageName),
                    'assembly' => $assembly
                ];
            }
        }
        return $this;
    }

    /**
     * load all mypackages
     */
    protected function buildMyPackages(array &$packages) {
        $path = base_path('mypackages');
        $files = glob(sprintf('%s/**/*/%s', $path, Consts::PACKAGE_COMPOSER_FILE));
        $len = strlen($path) + 1;
        foreach($files as $filename) {
            // $packagePath = substr(substr($filename, $len), 0, -13);
            $zentoConfigs = $this->getZentoConfigsFromJsonFile($filename);
            foreach($zentoConfigs as $packageName => $assembly) {
                $packages[$packageName] = [
                    'path' => substr($filename, 0, -13), 
                    'assembly' => $assembly
                ];
            }
        }
        return $this;
    }

    /**
     * from composer/autoload_classmap.php we can find out zento packages real path
     *
     * @return $this
     */
    protected function preparePackageNamePathMapping() {
        $this->packageNamePathMapping = [];
        if ($this->files->exists($path = $this->vendorPath.'/composer/autoload_classmap.php')) {
            #$path = '/var/www/test/vendor/composer/autoload_classmap.php';
            $classes = require_once($path);
            foreach($classes as $className => $classPath) {
                if (mb_strpos($className, "Zento\\") === 0) {
                    $parts = explode("\\", $className);
                    $nameParts = [array_shift($parts)]; 
                    $nameParts[] = array_shift($parts);
                    $packageName = strtolower(implode('_', $nameParts));
                    if (empty($this->packageNamePathMapping[$packageName])) {
                        $pathParts = explode("/", $classPath);
                        $pathParts = array_slice($pathParts, 0, count($pathParts) - count($parts));
                        $this->packageNamePathMapping[$packageName] = implode('/', $pathParts);
                    }
                    //normally will only support 2 level package name
                    //but just in case, we still support 3 level package name
                    $nameParts[] = array_shift($parts);
                    $packageName = strtolower(implode('_', $nameParts));
                    if (empty($this->packageNamePathMapping[$packageName])) {
                        $pathParts = explode("/", $classPath);
                        $pathParts = array_slice($pathParts, 0, count($pathParts) - count($parts));
                        $this->packageNamePathMapping[$packageName] = implode('/', $pathParts);
                    }
                }
            }
        }

        return $this;
    }


    protected function getZentoConfigsFromJsonFile($filename) {
        if ($this->files->exists($filename)) {
            $content = $this->files->get($filename);
            $json = json_decode($content, true);
            return $json['extra']['laravel']['zento'] ?? [];
        }
        return [];
    }

    /**
     * get package real path from package name
     *
     * @param string $packageName
     * @return string
     */
    protected function getZentoPackageRealPath($packageName) {
        $packageName = strtolower($packageName);
        return $this->packageNamePathMapping[$packageName] ?? '';
    }
}
