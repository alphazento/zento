<?php
/**
 *
 * @category   Framework support
 * @package    Base
 * @copyright
 * @license
 * @author      Yongcheng Chen tony@tonercity.com.au
 */

namespace Zento\Kernel\Booster\Events\Commands;

use Zento\Kernel\Facades\EventsManager;

class ListListener extends \Zento\Kernel\PackageManager\Console\Commands\Base
{
    /**
     * @var string
     */
    protected $signature = 'listeners';

    protected $description = 'List custom listener list';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Here are events and listeners you defined in the "listeners" items of your modules composer.json');
        $this->warn('Event: sort => listeners' . PHP_EOL);
        $listeners = EventsManager::getRawListeners();
        foreach ($listeners ?? [] as $key => $listeners) {
            $this->warn(str_pad($key, 45, ' '));
            sort($listeners);
            foreach ($listeners as $v) {
                $parts = explode(':::', $v);
                $this->info(sprintf('%s=>%s', str_pad(intval($parts[0]), 5, ' ', STR_PAD_LEFT), $parts[1]));
            }
            $this->info('');
        }
    }
}
