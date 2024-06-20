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

use Zento\Kernel\Facades\PackageManager;

class EnablePackage extends Base
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:enable {name : package name} {--depress-route-cache}
    {--ignore-migration} {--ignore-depends}';

    protected $description = 'Register package to the system';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $packageName = $this->argument('name');
        $refreshRouteCache = !$this->option('depress-route-cache');
        $ignoreMigration = $this->option('ignore-migration');
        $ignoreDepends = $this->option('ignore-depends');
        $assembly = PackageManager::rebuildPackages()->assembly($packageName);
        if (!$assembly) {
            $this->error(sprintf('Package [%s] is not found.', $this->argument('name')));
            return 1;
        }

        if (PackageManager::up($packageName, ['depends' => $ignoreDepends, 'migration' => $ignoreMigration])) {
            $this->info(sprintf('Package [%s] is ready.', $packageName));
            if ($refreshRouteCache) {
                $this->call('route:cache');
            }
        } else {
            $this->warn(sprintf('Package [%s] not able to enable or upgrade.', $packageName));
        }
        return 0;
    }
}
