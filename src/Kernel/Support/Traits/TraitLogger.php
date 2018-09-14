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

trait TraitLogger {
  protected function info($message, array $context =[]) {
    $context['mark'] = static::class;
    Log::info($message, $context);
  }

  protected function notice($message, array $context =[]) {
    $context['mark'] = static::class;
    Log::notice($message, $context);
  }

  protected function warning($message, array $context =[]) {
    $context['mark'] = static::class;
    Log::warning($message, $context);
  }

  protected function debug($message, array $context =[]) {
    $context['mark'] = static::class;
    Log::debug($message, $context);
  }

  protected function dump($message, array $context =[]) {
    $context['mark'] = static::class;
    Log::dump($message, $context);
  }

  protected function error($message, array $context =[]) {
    $context['mark'] = static::class;
    Log::error($message, $context);
  }

  protected function alert($message, array $context =[]) {
    $context['mark'] = static::class;
    Log::alert($message, $context);
  }

  protected function critical($message, array $context =[]) {
    $context['mark'] = static::class;
    Log::critical($message, $context);
  }
  
  protected function emergency($message, array $context =[]) {
    $context['mark'] = static::class;
    Log::emergency($message, $context);
  }
}
