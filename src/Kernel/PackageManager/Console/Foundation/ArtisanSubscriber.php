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
            'Illuminate\Console\Events\ArtisanStarting', 
            function ($event) {
                $artisan = $event->artisan;
                $accessor = self::bindAccessor($artisan);
                $accessor($artisan, 'laravel', new AlternativeConsoleApplication($artisan->getLaravel()));
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
