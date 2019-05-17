<?php
namespace Zento\Kernel\Support;

class ConsoleOutput 
{
    private static $command;

    public static function attachCommand(\Illuminate\Console\Command $command) {
      self::$command = $command;
    }

    public static function info($message) {
      if (self::$command) {
        self::$command->info($message);
      }
    }

    public static function comment($message) {
      if (self::$command) {
        self::$command->comment($message);
      }
    }

    public static function error($message) {
      if (self::$command) {
        self::$command->error($message);
      }
    }

    public static function warn($message) {
      if (self::$command) {
        self::$command->warn($message);
      }
    }

    public static function alert($message) {
      if (self::$command) {
        self::$command->alert($message);
      }
    }
}