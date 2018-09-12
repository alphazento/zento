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

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Log;

class Base extends Command
{
    public static function register($serviceProvider) {
        $class = static::class;
        $name = md5($class);
        app()->singleton($name, function ($app) use($class) {
            return $app[$class];
        });
        $serviceProvider->commands($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $method = method_exists($this, 'handle') ? 'handle' : 'fire';
        return $this->laravel->call([$this, $method]);
    }

    public function line($string, $style = null, $verbosity = null) {
        Log::mark(static::class, true);
        switch($style) {
          case 'comment':
            Log::notice($string);
            break;
          case 'info':
            Log::info($string);
            break;
          case 'warning':
            Log::warning($string);
            break;
          case 'error':
            Log::error($string);
            break;
        }
        return parent::line($string, $style, $verbosity);
    }
}
