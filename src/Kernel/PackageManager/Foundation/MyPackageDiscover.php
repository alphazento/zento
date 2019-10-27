<?php
/**
 *
 * @category   Framework
 * @package    Zento
 * @copyright
 * @license
 * @author      Yongcheng Chen yongcheng.chen@live.com
 */

namespace Zento\Kernel\PackageManager\Foundation;

use Cache;
use Zento\Kernel\Consts;
use Zento\Kernel\PackageManager\Model\ORM\Module;
use Zento\Kernel\PackageManager\Foundation\MyPackageManifest;

class MyPackageDiscover {
    use \Zento\Kernel\Support\Traits\TraitLogger;

    protected $app;
    protected $myPackageManifest;
    protected $manifest;

    public function __construct($app) {
        $this->app = $app ?: app();
        $this->myPackageManifest = new MyPackageManifest($this->app);
        $this->myPackageManifest->providers();
        $this->manifest = $this->myPackageManifest->manifest;
    }

    /**
     * get package's assembly settings
     * @param      string  $packageName  The Package Name
     * @return     Array
     */
    public function assembly($packageName) {
        return empty($this->manifest[$packageName]) ? null : $this->manifest[$packageName];
    }

    /**
     * get all packages' assembly settings
     * @param      string  $packageName  The modula name
     * @return     Array
     */
    public function assemblies() {
        return $this->manifest;
    }

    public function rebuildPackages() {
        $this->myPackageManifest->build();
        $this->myPackageManifest->providers();
        $this->manifest = $this->myPackageManifest->manifest;
        return $this;
    }

    /**
     * Gets the package path.
     *
     * @param      string  $packageName  The modula name
     * @param      mixed   $subPaths    The sub paths array or string
     *
     * @return     string  The package path.
     */
    public function packagePath(string $packageName, $subPaths = []) {
        $path = empty($this->manifest[$packageName]) ? null : $this->manifest[$packageName]['module_path'];
        if (!$path) {
            return null;
        }
        if (!is_array($subPaths)) {
            $subPaths = [$subPaths];
        }
       
        array_unshift($subPaths, $path);
        return implode(DIRECTORY_SEPARATOR, $subPaths);
    }

    /**
     * Gets the package's views path.
     *
     * @param      string  $packageName  The modula name
     *
     * @return     string  The package's views path.
     */
    public function packageViewsPath(string $packageName) {
        return $this->packagePath($packageName, Consts::PACKAGE_VIEWS_FOLDER);
    }

    public function myPackageRootPath(string $packageName, array $subPaths = []) {
        array_unshift($subPaths, $packageName);
        return sprintf('%s/%s', base_path(Consts::MY_PACKAGES_ROOT_FOLDER), implode('/', $subPaths));
    }
}
