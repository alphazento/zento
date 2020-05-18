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

use Illuminate\Support\Facades\Route;
use Zento\Kernel\Consts;
use Zento\Kernel\Facades\EventsManager;
use Zento\Kernel\Facades\ThemeManager;
use Zento\Kernel\PackageManager\Foundation\MyPackageDiscover;
use Zento\Kernel\PackageManager\Foundation\PackageMigrator;
use Zento\Kernel\PackageManager\Model\ORM\PackageConfig;
use Zento\Kernel\PackageManager\Model\TopSort;

class PackageManagerService extends MyPackageDiscover
{
    use \Zento\Kernel\Support\Traits\TraitLogger;

    protected $kernelEnabled = false;
    protected $bootedCallbacks = [];
    protected $packageConfigs;
    protected $routesFolders = [];
    protected $themeRoutesFolders = [];

    protected $runningInConsole = false;

    /**
     * some command line will create a new app, use this flag to not class_allias twice
     *
     * @var boolean
     */
    protected static $injected = false;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->runningInConsole = $app->runningInConsole();
    }

    public function getActivePackageConfigs()
    {
        return $this->packageConfigs;
    }

    public function getPackageConfig($packageName)
    {
        return $this->packageConfigs[$packageName] ?? [];
    }
    /**
     * load enabled packages from database configs
     *
     * @param boolean $forceReload
     * @return array $packages \Zento\Kernel\PackageManager\Model\ORM\PackageConfig
     */
    public function loadPackagesConfigs($forceReload = false)
    {
        try {
            $packages = null;
            if ($this->app->bound('cache')) {
                $cache = $this->app->make('cache');
                if ($forceReload || !$cache->has(Consts::CACHE_KEY_ENABLED_PACKAGES)) {
                    $packages = PackageConfig::where('enabled', 1)
                        ->orderBy('sort')
                        ->get()
                        ->keyBy('name')
                        ->toArray();
                    $cache->forever(Consts::CACHE_KEY_ENABLED_PACKAGES, serialize($packages));
                } else {
                    $packages = $cache->get(Consts::CACHE_KEY_ENABLED_PACKAGES, null);
                    $packages = unserialize($packages);
                }
                foreach ($packages as $name => $config) {
                    if ($name == Consts::ZENTO_KERNEL_PACKAGE_NAME) {
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

    public function deletePackageConfig($item_id)
    {
        PackageConfig::where('id', $item_id)->delete();
        return $this;
    }

    public function isKernelEnabled()
    {
        return $this->kernelEnabled;
    }

    /**
     * inject packages to app
     *
     * @param \Illuminate\Support\ServiceProvider $serviceProvider
     * @return $this
     */
    public function inject(\Illuminate\Support\ServiceProvider $serviceProvider)
    {
        $this->packageConfigs = $this->loadPackagesConfigs();
        if (count($this->packageConfigs ?? [])) {
            foreach ($this->packageConfigs as $name => $packageConfig) {
                $assembly = $this->assembly($name);
                if (!empty($assembly)) {
                    $this->mountPackage($name, $assembly, $serviceProvider);
                } else {
                    $this->warning(sprintf("Package [%s] has an empty assembly configs.", $name));
                }
            }
        } else {
            $this->registerSelf($serviceProvider);
            $this->alert(sprintf("You haven't enable package [%s]", Consts::ZENTO_KERNEL_PACKAGE_NAME));
        }
        self::$injected = true;
        return $this;
    }

    protected function registerSelf(\Illuminate\Support\ServiceProvider $serviceProvider)
    {
        $this->mountPackage(Consts::ZENTO_KERNEL_PACKAGE_NAME,
            [
                "commands" => [
                    '\Zento\Kernel\PackageManager\Console\Commands\MakePackage',
                    '\Zento\Kernel\PackageManager\Console\Commands\EnablePackage',
                    '\Zento\Kernel\PackageManager\Console\Commands\DisablePackage',
                    '\Zento\Kernel\ThemeManager\Console\Commands\ListTheme',
                    '\Zento\Kernel\Booster\Events\Commands\ListListener',
                ],
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
                $this->class_alias($class, $alias);
            }
        }

        //register package's providers
        if (isset($assembly['providers'])) {
            foreach ($assembly['providers'] as $provider) {
                if (class_exists($provider)) {
                    $this->app->register($provider);
                } else {
                    if ($this->runningInConsole) {
                        echo sprintf('%s not exist', $provider) . PHP_EOL;
                    }
                }
            }
        }

        if (isset($assembly['listeners']) && !EventsManager::isCached()) {
            EventsManager::addEventListeners($assembly['listeners']);
        }

        //register middleware
        foreach ($assembly['middlewares'] ?? [] as $name => $class) {
            $this->app['router']->aliasMiddleware($name, $class);
        }

        foreach ($assembly['middlewaregroup'] ?? [] as $groupName => $classes) {
            if (isset($classes['main']) && count($classes['main'])) {
                $this->app['router']->middlewareGroup($groupName, $classes['main']);
            }
            if (isset($classes['pre']) && count($classes['pre'])) {
                foreach ($classes['pre'] as $class) {
                    $this->app['router']->prependMiddlewareToGroup($groupName, $class);
                }
            }
            if (isset($classes['post']) && count($classes['post'])) {
                foreach ($classes['post'] as $class) {
                    $this->app['router']->pushMiddlewareToGroup($groupName, $class);
                }
            }
        }

        //in _zento_assembly.php
        // "views" => [
        //     "console" => true,
        //     "namespaces" => [
        //         'namespace' => 'relativepath'
        //     ],
        // ],
        $canRegisterViews = true;
        if ($this->runningInConsole) {
            //register package's commands
            if (isset($assembly['commands'])) {
                // print_r($assembly['commands']);
                foreach ($assembly['commands'] as $command) {
                    call_user_func_array([$command, 'register'], [$serviceProvider]);
                }
            }

            //register publishes
            $publicPath = $this->packagePath($packageName, Consts::PACKAGE_ASSETS_FOLDER);
            if (file_exists($publicPath)) {
                $serviceProvider->preparePublishes(
                    [
                        $publicPath => public_path(strtolower($packageName)),
                    ]
                );
            }
            $canRegisterViews = $assembly['views']['console'] ?? false;
        }

        if ($canRegisterViews) {
            $viewLocation = $this->packageViewsPath($packageName);
            if (file_exists($viewLocation)) {
                if (empty($assembly['theme'])) {
                    if ($namespaces = ($assembly['views']['namespaces'] ?? false)) {
                        foreach ($namespaces as $namespace => $relativePath) {
                            ThemeManager::addNameSpace($namespace, sprintf('%s/%s', $viewLocation, $relativePath));
                        }
                    } else {
                        ThemeManager::addLocation($viewLocation);
                    }
                }
            }
        }

        if (!$this->app->routesAreCached()) {
            if ($routesFolder = $this->packagePath($packageName, Consts::PACKAGE_ROUTES_FOLDER)) {
                if (file_exists($routesFolder)) {
                    $this->routesFolders[] = $routesFolder;
                }
            }
        }
    }

    /**
     * map all routes
     *
     * @return void
     */
    public function mapRoutes()
    {
        if (!$this->app->routesAreCached()) {
            $routes = ['api.php', 'web.php', 'admin_api.php', 'admin_web.php'];
            foreach ($this->routesFolders as $folder) {
                foreach ($routes as $route) {
                    $file = $folder . '/' . $route;
                    if (file_exists($file)) {
                        require $file;
                    }
                }
            }
        }
    }

    protected function sortDependancyOrder($array)
    {
        $topSort = new TopSort();
        foreach ($array as $item) {
            $topSort->add($item[0], $item[1]);
        }
        return $topSort->sort();
    }

    /**
     * sort packages base on their dependencies
     * @param  array  &$packages [description]
     * @return [type]           [description]
     */
    public function resolvePackagDependencies()
    {
        $packages = $this->loadPackagesConfigs();
        $depends = [];
        foreach ($packages as $packageName => $package) {
            if ($packageName == Consts::ZENTO_KERNEL_PACKAGE_NAME) {
                $depends[] = [$packageName, []];
            } else {
                $assembly = $this->assembly($packageName);
                $mydepends = isset($assembly['depends']) ? $assembly['depends'] : [Consts::ZENTO_KERNEL_PACKAGE_NAME];
                if (!empty($assembly['theme']) && !is_numeric($assembly['theme'])
                    && $assembly['theme'] !== true && $assembly['theme'] !== false) {
                    $mydepends = array_merge($mydepends, explode(',', $assembly['theme']));
                }
                $depends[] = [$packageName, $mydepends];
            }
        }

        $sorts = $this->sortDependancyOrder($depends);
        $packages = PackageConfig::where('enabled', 1)
        // ->orderByRaw(sprintf("FIELD(`name`,'%s') ASC", implode("','", $sorts)))
            ->get();
        $sort = 0;
        $sorts = array_map('strtolower', $sorts);
        foreach ($packages as $package) {
            $package->sort = array_search(strtolower($package->name), $sorts) ?? 0;
            $package->update();
        }
    }

    /**
     * migrate to the latest version which described in config/settings.php (version)
     * @param string $packageName
     */
    public function up(string $packageName)
    {
        $packageConfig = $this->findPackageConfigOrNew($packageName, true);
        $currentVersion = $packageConfig->version;
        $this->info(sprintf('[%s] current version:%s', $packageName, $packageConfig->version));
        $m = new PackageMigrator();
        if ($m->migrate($packageConfig)) {
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
            $this->resolvePackagDependencies();
            // return $currentVersion === $latestVersion;
            return true;
        }

        return false;
    }

    /**
     * disable the specified package
     * @param string $packageName
     */
    public function down(string $packageName)
    {
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
     * find a package from db, if not exist, create new one
     *
     * @param      string  $packageName  The package name
     * @param      boolean $new if not exists, will create new empty object
     *
     * @return     \Zento\Kernel\PackageManager\Model\ORM\PackageConfig  The packageconfig object
     */
    public function findPackageConfigOrNew(string $packageName, $new = false)
    {
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
    public function getNameSpace($packageName)
    {
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
    public function splitPackageName($packageName)
    {
        $parts = explode('_', $packageName);
        if (count($parts) < 2) {
            throw new \Exception(sprintf('Package name:[%s] must format as [Vendoer_Package] or [Vendoer_Package_Sub]', $packageName));
        }
        $parts = array_map(function ($v) {
            return ucfirst($v);
        }, $parts);
        return $parts;
    }

    public function class_alias($class_name, $alias)
    {
        if (!self::$injected) {
            \class_alias($class_name, $alias);
        }
    }
}
