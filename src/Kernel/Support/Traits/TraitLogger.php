<?php 
 /**
  *
  * @category   Framework support
  * @package    Zento
  * @copyright
  * @license
  * @author      Yongcheng Chen yongcheng.chen@live.com
  */
namespace Zento\Kernel\Support\Traits;

use Log;
use Zento\Kernel\Support\ConsoleOutput;

trait TraitLogger {
  protected function info($message, array $context =[]) {
    $context['mark'] = static::class;
    Log::info($message, $context);
    ConsoleOutput::info($message);
  }

  protected function notice($message, array $context =[]) {
    $context['mark'] = static::class;
    Log::notice($message, $context);
    ConsoleOutput::comment($message);
  }

  protected function warning($message, array $context =[]) {
    $context['mark'] = static::class;
    Log::warning($message, $context);
    ConsoleOutput::warn($message);
  }

  protected function debug($message, array $context =[]) {
    $context['mark'] = static::class;
    Log::debug($message, $context);
    ConsoleOutput::warn($message);
  }

  protected function dump($message, array $context =[]) {
    $context['mark'] = static::class;
    Log::dump($message, $context);
    ConsoleOutput::comment($message);
  }

  protected function error($message, array $context =[]) {
    $context['mark'] = static::class;
    Log::error($message, $context);
    ConsoleOutput::error($message);
  }

  protected function alert($message, array $context =[]) {
    $context['mark'] = static::class;
    Log::alert($message, $context);
    ConsoleOutput::alert($message);
  }

  protected function critical($message, array $context =[]) {
    $context['mark'] = static::class;
    Log::critical($message, $context);
    ConsoleOutput::alert($message);
  }
  
  protected function emergency($message, array $context =[]) {
    $context['mark'] = static::class;
    Log::emergency($message, $context);
    ConsoleOutput::alert($message);
  }
}
