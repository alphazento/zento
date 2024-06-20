<?php
/**
 *
 * @category   Framework support
 * @package    Zento
 * @copyright
 * @license
 * @author      Yongcheng Chen yongcheng.chen@live.com
 */

namespace Zento\Kernel\PackageManager\Console\Foundation;

use Event;
use Zento\Kernel\Facades\ThemeManager;

class ArtisanSubscriber
{
    public function subscribe()
    {
        Event::listen(
            'Illuminate\Console\Events\CommandFinished',
            function ($event) {
                if ($event->command === 'package:discover') {
                    (new Commands\PackageDiscoverCommandRunAfter($event->input, $event->output))->discoverMyPackages();
                }
            }
        );
        Event::listen(
            'Illuminate\Console\Events\CommandStarting',
            function ($event) {
                ThemeManager::setTheme(env('CONSOLE_THEME'));
            }
        );
    }
}
