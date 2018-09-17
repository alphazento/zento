<?php
/**
 *
 * @category   Framework
 * @package    Zento
 * @copyright
 * @license
 * @author      Yongcheng Chen yongcheng.chen@live.com
 */

namespace Zento\Kernel\PackageManager;

use Cache;

use Zento\Kernel\Consts;
use Zento\Kernel\PackageManager\Model\ORM\PackageConfig;
use Zento\Kernel\PackageManager\Model\PackageMigrator;
use Zento\Kernel\PackageManager\Foundation\MyPackageDiscover;
use Zento\Kernel\Facades\EventsManager;

class PackageManagerService extends MyPackageDiscover {
    use \Zento\Kernel\Support\Traits\TraitLogger;

    protected $kernelEnabled = false;
    protected $bootedCallbacks = [];
    protected $packageConfigs;

    public function __construct($app) {
        parent::__construct($app);
    }

    public function getActivePackageConfigs() {
        return $this->packageConfigs;
    }

    /**
     * load enabled packages from database configs
     *
     * @param boolean $forceReload
     * @return array $packages \Zento\Kernel\PackageManager\Model\ORM\PackageConfig
     */
    public function loadPackagesConfigs($forceReload = false) {
        try {
            $packages = null;
            if ($this->app->bound('cache')) {
                $cache = $this->app->make('cache');
                if ($forceReload || !$cache->has(Consts::CACHE_KEY_ENABLED_PACKAGES)) {
                    $packages = PackageConfig::where('enabled', 1)->orderBy('sort')->get();
                    $cache->forever(Consts::CACHE_KEY_ENABLED_PACKAGES, serialize($packages));
                } else {
                    $packages = $cache->get(Consts::CACHE_KEY_ENABLED_PACKAGES, null);
                    $packages = unserialize($packages);
                }
                foreach($packages as $config) {
                    if ($config->name == Consts::ZENTO_KERNEL_PACKAGE_NAME) {
                        $this->kernelEnabled = true;
                        break;
                    }
                }
            }

            return $packages;
        } catch (\Exception $e) {
            $this->kernelEnabled = false;
            return null;
        }
    }

    public function isKernelEnabled() {
        return $this->kernelEnabled;
    }

    /**
     * inject packages to app
     * 
     * @param \Illuminate\Support\ServiceProvider $serviceProvider
     * @return $this
     */
    public function inject(\Illuminate\Support\ServiceProvider $serviceProvider) {
        $this->packageConfigs = $this->loadPackagesConfigs();
        $this->registerSelf($serviceProvider);

        if (count($this->packageConfigs ?? [])) {
            foreach($this->packageConfigs as $packageConfig) {
                $assembly = $this->assembly($packageConfig->name);
                if (!empty($assembly)) {
                    $this->mountPackage($packageConfig->name, $assembly, $serviceProvider);
                } else {
                    $this->warning(sprintf("Package [%s] has an empty assembly configs.", $packageConfig->name));
                }
            }
            $this->packageConfigs = null; 
        } else {
            $this->alert(sprintf("You haven't enable package [%s]", Consts::ZENTO_KERNEL_PACKAGE_NAME));
        }
        return $this;
    }

    protected function registerSelf(\Illuminate\Support\ServiceProvider $serviceProvider) {
        $this->mountPackage(Consts::ZENTO_KERNEL_PACKAGE_NAME, 
            [
                "commands" => [
                    '\Zento\Kernel\PackageManager\Console\Commands\MakePackage',
                    '\Zento\Kernel\PackageManager\Console\Commands\EnablePackage',
                    '\Zento\Kernel\PackageManager\Console\Commands\DisablePackage',
                    '\Zento\Kernel\Booster\Events\Command\ListListener'
                ]
            ],
            $serviceProvider);
    }

    /**
     * Mount a package
     *
     * @param \Zento\Kernel\PackageManager\Model\ORM\PackageConfig $packageConfig
     * @param \Illuminate\Support\ServiceProvider $serviceProvider
     * @return void
     */
    protected function mountPackage(string $packageName,
        array $assembly,
        \Illuminate\Support\ServiceProvider $serviceProvider) {
        //register aliases
        if (isset($assembly['aliases'])) {
            foreach ($assembly['aliases'] as $alias => $class) {
                if (!class_exists($alias)) {
                    class_alias($class, $alias);
                }
            }
        }

        //register package's providers
        if (isset($assembly['providers'])) {
            foreach ($assembly['providers'] as $provider) {
                $this->app->register($provider);
            }
        }

        if (isset($assembly['listeners']) && !EventsManager::isCached()) {
            EventsManager::addEventListeners($assembly['listeners']);
        }

        //register routes
        if (!$this->app->runningInConsole()) {
             //register middleware
            if (isset($assembly['middlewares'])) {
                foreach ($assembly['middlewares'] as $middleware => $classOrClasses) {
                    if (is_array($classOrClasses) && count($classOrClasses)) {
                        if (isset($classOrClasses['main']) && count($classOrClasses['main'])) {
                            $app['router']->middlewareGroup($middleware, $classOrClasses['main']);
                        }
                        if (isset($classOrClasses['pre']) && count($classOrClasses['pre'])) {
                            foreach($classOrClasses['pre'] as $class) {
                                $this->app['router']->prependMiddlewareToGroup($middleware, $class);
                            }
                        }
                        if (isset($classOrClasses['post']) && count($classOrClasses['post'])) {
                            foreach($classOrClasses['post'] as $class) {
                                $this->app['router']->pushMiddlewareToGroup($middleware, $class);
                            }
                        }
                    } elseif (is_string($classOrClasses)) {
                        $this->app['router']->aliasMiddleware($middleware, $classOrClasses);
                    }
                }
            }
            
            $routesFile = $this->packagePath($packageName, Consts::PACKAGE_ROUTE_FILE);
            if (file_exists($routesFile)) {
                require_once $routesFile;
            }
        } else {
            //register package's commands
            if (isset($assembly['commands'])) {
                foreach ($assembly['commands'] as $command) {
                    call_user_func_array([$command, 'register'], [$serviceProvider]);
                }
            }

            //register publishes
            $publicPath = $this->packagePath($packageName, Consts::PACKAGE_ASSETS_FOLDER);
            if (file_exists($publicPath)) {
                $serviceProvider->preparePublishes(
                    [
                        $publicPath => public_path(strtolower($packageName))
                    ]
                );
            }
        }
    }

    /**
     * sort packages base on their dependencies
     * @param  array  &$packages [description]
     * @return [type]           [description]
     */
    protected function sortPackages(array &$packages) {
        $depended = [];
        foreach ($packages as $name => $package) {
            $assembly = $this->assembly($name);
            if (isset($assembly['dependency'])) {
                foreach($assembly['dependency'] as $depend) {
                    if (!isset($packages[$depend])) {
                        throw new \Exception(sprintf('Package %s does not exists.', $depend));
                    }
                    if (!isset($depended[$depend])) {
                        $depended[$depend] = [];
                    }
                    $depended[$depend][] = $name;
                    if (isset($depended[$name])) {
                        $tmp = $depended[$name];
                        unset($depended[$name]);
                        $depended[$name] = $tmp;
                    }
                }
            } else {
                $sort[] = $name;
            }
        }
        $sort = array_unique($sort);

        foreach($depended as $key => $subs) {
            if(in_array($key, $sort)) {
                foreach($subs as $item) {
                    $sort[] = $item;
                }
            }
        }

        return $sort;
    }

    /**
     * migrate to the latest version which described in config/settings.php (version) 
     * @param string $packageName
     */
    public function up(string $packageName) {
        $packageConfig = $this->findPackageConfigOrNew($packageName, true);
        $currentVersion = $packageConfig->version;
        $this->info(sprintf('[%s] current version:%s', $packageName, $packageConfig->version));
        $m = new PackageMigrator();
        $m->migrate($packageConfig);
        $packageConfig->enabled = true;
        $packageConfig->save();
        $latestVersion = $packageConfig->version;

        if ($currentVersion !== $latestVersion) {
            $this->info(sprintf('[%s] has been updated from version [%s] -> [%s]', $packageName, $currentVersion, $latestVersion));
            if ($this->app->bound('cache')) {
                $cache = $this->app->make('cache');
                $cache->forget(Consts::CACHE_KEY_ENABLED_PACKAGES);
            }
        } else {
            $this->warning(sprintf('[%s] stay at current version [%s]', $packageName, $currentVersion));
        }
        return $this;
    }

    /**
     * disable the specified package
     * @param string $packageName
     */
    public function down(string $packageName) {
        $packageConfig = $this->findPackageConfigOrNew($packageName);
        if ($packageConfig) {
            $packageConfig->enabled = false;
            $packageConfig->save();
            if ($this->app->bound('cache')) {
                $cache = $this->app->make('cache');
                $cache->forget(Consts::CACHE_KEY_ENABLED_PACKAGES);
            }
        }
        return $this;
    }


    /**
     * register a callback, and it will be called after app booted
     * if callback is null, it will fire all callbacks
     */
    public function booted($callback=null) {
        if ($callback) {
            $this->bootedCallbacks[] = $callback;
        } else {
            $this->fireAppCallbacks();
        }
    }

    protected function fireAppCallbacks() {
        foreach ($this->bootedCallbacks as $callback) {
            call_user_func($callback, $this->app);
        }
    }

    /**
     * find a package from db, if not exist, create new one
     * 
     * @param      string  $packageName  The package name
     * @param      boolean $new if not exists, will create new empty object
     *
     * @return     \Zento\Kernel\PackageManager\Model\ORM\PackageConfig  The packageconfig object
     */
    public function findPackageConfigOrNew(string $packageName, $new = false) {
        $packageConfig = null;
        try {
            $packageConfig = PackageConfig::where('name', $packageName)->first();
        } catch (\Exception $e) {
            
        }
        if (!$packageConfig && $new) {
            $packageConfig = new PackageConfig();
            $packageConfig->name = $packageName;
        }
        return $packageConfig;
    }

    /**
     * get a package's spacename from package name
     */
    public function getNameSpace($packageName) {
        return implode('\\', $this->splitPackageName($packageName)); 
    }

    /**
     * split package name from VendoerName_Package to [Organization,Package]
     *
     * @param      string      $packageName  The package name
     *
     * @throws     \Exception  if package name doesn't follow the rule
     *
     * @return     array[string]   array of package name elements
     */
    public function splitPackageName($packageName) {
        $parts = explode('_', $packageName);
        if (count($parts) < 2) {
            throw new \Exception(sprintf('Package name:[%s] must format as [Vendoer_Package] or [Vendoer_Package_Sub]', $packageName));
        }
        $parts = array_map(function($v) {
            return ucfirst($v);
        }, $parts);
        return $parts;
    }
}
