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

use Illuminate\Support\Str;
use Zento\Kernel\Consts;
use Zento\Kernel\PackageManager\Foundation\MyPackageManifest;

class MyPackageDiscover
{
    use \Zento\Kernel\Support\Traits\TraitLogger;

    protected $app;
    protected $myPackageManifest;
    protected $manifest;
    protected $basePath;

    public function __construct($app)
    {
        $this->app = $app ?: app();
        $this->myPackageManifest = new MyPackageManifest($this->app);
        $this->myPackageManifest->providers();
        $this->manifest = $this->myPackageManifest->manifest;
        $this->basePath = base_path();
    }

    /**
     * get package's assembly settings
     * @param      string  $packageName  The Package Name
     * @return     Array
     */
    public function assembly($packageName)
    {
        return empty($this->manifest[$packageName]) ? null : $this->manifest[$packageName];
    }

    /**
     * get all packages' assembly settings
     * @param      string  $packageName  The modula name
     * @return     Array
     */
    public function assemblies()
    {
        return $this->manifest;
    }

    public function rebuildPackages()
    {
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
    public function packagePath(string $packageName, $subPaths = [])
    {
        $path = $this->manifest[$packageName]['module_path'] ?? null;
        if (!$path) {
            return null;
        }
        if (!is_array($subPaths)) {
            $subPaths = [$subPaths];
        }

        array_unshift($subPaths, $path);
        $path = implode(DIRECTORY_SEPARATOR, $subPaths);
        // if (!Str::startsWith($path, $this->basePath)) {
        //     $path = base_path($path);
        // }
        return $this->relevantPath($path);
    }

    public function relevantPath(string $path)
    {
        if (Str::startsWith($path, $this->basePath)) {
            $path = Str::replaceFirst($this->basePath, '', $path);
        }
        $path = Str::of($path)->ltrim('/');
        return (string) $path;
    }

    public function absolutePath(string $path)
    {
        if (!Str::startsWith($path, $this->basePath)) {
            return base_path($path);
        }
        return $path;
    }

    /**
     * Gets the package's views path.
     *
     * @param      string  $packageName  The modula name
     *
     * @return     string  The package's views path.
     */
    public function packageViewsPath(string $packageName)
    {
        return $this->packagePath($packageName, Consts::PACKAGE_VIEWS_FOLDER);
    }

    public function myPackageRootPath(string $packageName, array $subPaths = [])
    {
        array_unshift($subPaths, $packageName);
        return sprintf('%s/%s', base_path(Consts::MY_PACKAGES_ROOT_FOLDER), implode('/', $subPaths));
    }
}
