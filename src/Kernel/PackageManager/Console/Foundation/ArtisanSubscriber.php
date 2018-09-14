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
use Closure;

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
    }

    public static function bindAccessor($origin) {
        $accessor = function ($target, $property_name, $newProperty=null) {
            if ($newProperty != null) {
                $target->$property_name = $newProperty;  
            }
            return $target->$property_name;
        };
        return Closure::bind($accessor, null, get_class($origin));
    }
}
