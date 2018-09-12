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
        Log::mark(static::class)->info($message, $context);
  }

  protected function notice($message, array $context =[]) {
      Log::mark(static::class)->notice($message, $context);
  }

  protected function warning($message, array $context =[]) {
      Log::mark(static::class)->warning($message, $context);
  }

  protected function debug($message, array $context =[]) {
      Log::mark(static::class)->debug($message, $context);
  }

  protected function dump($message, array $context =[]) {
      Log::mark(static::class)->dump($message, $context);
  }

  protected function error($message, array $context =[]) {
      Log::mark(static::class)->error($message, $context);
  }

  protected function alert($message, array $context =[]) {
      Log::mark(static::class)->alert($message, $context);
  }

  protected function critical($message, array $context =[]) {
      Log::mark(static::class)->critical($message, $context);
  }
  
  protected function emergency($message, array $context =[]) {
      Log::mark(static::class)->emergency($message, $context);
  }
}
