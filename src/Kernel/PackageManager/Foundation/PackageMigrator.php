<?php
namespace Zento\Kernel\PackageManager\Model;

use Zento\Kernel\PackageManager\Model\ORM\PackageConfig;
use Zento\Kernel\Consts;
use Zento\Kernel\Facades\PackageManager;

class PackageMigrator {
    use \Zento\Kernel\Support\Traits\TraitLogger;
    /**
     * 1. Find if there's new database setup script and run it.
     * 2. Find if there's setup/up.php script and run it.
     */
    public function migrate(PackageConfig &$packageConfig) {
        $packageName = $packageConfig->name;
        $assembly = PackageManager::assembly($packageName);

        $version  = isset($assembly['version']) ? $assembly['version'] : '0';
        
        if ($version === $packageConfig->version) {
            $this->warning("There's no new version found.");
            $file = PackageManager::packagePath($packageName, ['setup', 'up.php']);
            if (file_exists($file)) {
                require_once($file);
            }
            return true;
        }

        $toVersion = $this->versionToNumber($version);
        $fromVersion = $packageConfig->version == null ? -1 : $this->versionToNumber($packageConfig->version);
        
        $versions = $this->getVersions($packageName, $fromVersion, $toVersion);

        ksort($versions);
        foreach ($versions as $ver => $params) {
            list($path, $version) = $params;
            $packageConfig->version = $version;
            if ($this->applyMigration($path)) {
                $packageConfig->save();
            } else {
                $this->error('Package migration was stop at version:' . $version);
                return false;
            }
        }

        $file = PackageManager::packagePath($packageName, ['setup', 'up.php']);
        if (file_exists($file)) {
            require_once($file);
        }
        $packageConfig->theme = isset($assembly['theme']) ? $assembly['theme'] : '' ;
        $packageConfig->is_theme = !! $packageConfig->theme;
        $packageConfig->version = $version;
        $packageConfig->save();
        return true;
    }

    /**
     * apply database migration script
     */
    protected function applyMigration($path) {
        $files = glob($path  . '/*.php');
        ksort($files);
        $dbMigrations = [];
        foreach($files as $file) {
            include_once($file);
            $className = $this->genClassName(substr(basename($file), 0, -4));
            $instance = new $className;
            $dbMigrations[] = $instance;
            try {
                $instance->up();
                $this->info($className . ' up...');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
                $this->info($e->getTraceAsString());
                $this->warning($className . ' fail to enable or upgrade');
                $this->info('Rollback...');
                foreach($dbMigrations as $instance) {
                    $instance->down();
                }
                return false;
            }
        }
        return true;
    }

    protected function genClassName($filename) {
        if (preg_match('/[0-9_]+(.*)/', $filename, $matches)) {
             $matches = explode('_', $matches[1]);
             return '\\' . implode('', array_map(function($v) {
                    return ucfirst($v);
                }, $matches));
        }
        return false;
    }

    /**
     * get all package's versions, and find all database script between these versions.
     *
     * @param string $name
     * @param integer $fromVersion
     * @param integer $toVersion
     * @return void
     */
    protected function getVersions(string $name, int $fromVersion, int $toVersion) {
        $paths = glob(PackageManager::packagePath($name, [Consts::PACKAGE_SETUP_DATABASE_FOLDER, '*']), GLOB_ONLYDIR);
        $results = [];
        foreach($paths as $path) {
            $vers = explode(DIRECTORY_SEPARATOR, $path);
            $version = end($vers);
            $versionNumber = $this->versionToNumber($version);
            if ($versionNumber > $fromVersion && $versionNumber <= $toVersion) {
                $results[$versionNumber] = [$path, $version];
            }
        }
        return $results;
    }

    /**
     * convert a version string to a sortable number, so we can run these script sort by version number
     */
    protected function versionToNumber(string $version) {
        if ($version == 'init') {
            return 0;
        }
        $numbers = explode('.', $version);
        $version = 0;
        for ($i = 0; $i < count($numbers); $i++) {
            $version = ($version + $numbers[$i]) * 1000;
        }
        return $version;
    }
}
