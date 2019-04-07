<?php
/**
 *
 * @category   Framework support
 * @package    Zento
 * @copyright
 * @license
 * @author      Yongcheng Chen yongcheng.chen@live.com
 */

namespace Zento\Kernel\ThemeManager\Console\Commands;

use Zento\Kernel\Facades\ThemeManager;

class ListTheme extends \Zento\Kernel\PackageManager\Console\Commands\Base
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:themes';

    protected $description = "Find out theme packages.";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $packageConfig = ThemeManager::availableThemes();
        $this->info('Theme packages:');
        dd($packageConfig);
    }
}
