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

use Zento\Kernel\Consts;
use Zento\Kernel\Facades\PackageManager;

class MakePackage extends Base
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:package
      {name : unique package name}';

    protected $description = 'Create a whole new package';

    private function getPackageSubFolders()
    {
        $folders   = ['Model', 'config', 'database'];
        $folders[] = implode(DIRECTORY_SEPARATOR, ['Console', 'Commands']);
        $folders[] = implode(DIRECTORY_SEPARATOR, ['Http', 'Controllers']);
        $folders[] = implode(DIRECTORY_SEPARATOR, ['Http', 'Middleware']);
        $folders[] = implode(DIRECTORY_SEPARATOR, ['Providers', 'Facades']);
        $folders[] = implode(DIRECTORY_SEPARATOR, ['Services']);
        $folders[] = implode(DIRECTORY_SEPARATOR, ['resources', 'views']);
        $folders[] = implode(DIRECTORY_SEPARATOR, ['resources', 'public', 'js']);
        $folders[] = implode(DIRECTORY_SEPARATOR, ['resources', 'public', 'css']);
        $folders[] = implode(DIRECTORY_SEPARATOR, ['resources', 'public', 'font']);
        $folders[] = implode(DIRECTORY_SEPARATOR, ['resources', 'public', 'images']);
        return $folders;
    }

    private function makeSubFolders($pathParts)
    {
        $packageVendorPath = PackageManager::myPackageRootPath($pathParts[0]);
        if (!file_exists($packageVendorPath)) {
            mkdir($packageVendorPath, 0755, true);
        }

        $packagePath = PackageManager::myPackageRootPath(implode(DIRECTORY_SEPARATOR, $pathParts));
        if (file_exists($packagePath)) {
            $this->error(sprintf('Package path=[%s] already exists, please check first.', $packagePath));
            die;
        }

        mkdir($packagePath, 0755, true);
        $folders = $this->getPackageSubFolders();

        foreach ($folders as $folder) {
            $path = $packagePath . DIRECTORY_SEPARATOR . $folder;
            mkdir($path, 0755, true);
        }
        return $this;
    }

    private function initExamples($packageRootPath)
    {
        $stubsPath = sprintf('%s/../../stubs', __DIR__);
        $items = [
            [
                $stubsPath . '/routes.php.example', //from
                PackageManager::myPackageRootPath($packageRootPath, ['routes.php.example']),                   //to
            ],
            [
                $stubsPath . '/gitignore.example', //from
                PackageManager::myPackageRootPath($packageRootPath, ['.gitignore']),                   //to
            ],
            [
                $stubsPath . '/composer.json.example', //from
                PackageManager::myPackageRootPath($packageRootPath, ['composer.json']),                   //to
            ],
        ];

        foreach ($items as $item) {
            copy(...$item);
        }
        return $this;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Start creating package.');
        $name = ucwords($this->argument('name'));
        $pathParts = PackageManager::splitPackageName($name);
        $packageRootPath = implode(DIRECTORY_SEPARATOR, $pathParts);
        $this->makeSubFolders($pathParts)->initExamples($packageRootPath);
        $this->warn(sprintf('Package [%s] has been created.', $name));
        $this->warn(sprintf('Please check %s ', PackageManager::myPackageRootPath($packageRootPath)));
    }
}
