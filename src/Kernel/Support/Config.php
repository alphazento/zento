<?php
namespace Zento\Kernel\Support;

use Cache;
use Zento\Kernel\Model\DB\Config as ConfigModel;

class Config {
    public function get($key) {
        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $config = ConfigModel::where('path', '=', $key)
            ->first();
        if ($config) {
            Cache::forever($key, $config->value);
            return $config->value;
        }
        return null;
    }

    public function has($key) {
        if (Cache::has($key)) {
            return true;
        }

        $config = ConfigModel::where('path', '=', $key)
            ->first();
        if ($config) {
            return true;
        }
        return false;
    }

    public function set($key, $value) {
        $config = ConfigModel::where('path', '=', trim($key))
                ->first();
        if (!$config) {
            $config = new ConfigModel();
            $config->path = $key;
        }
        $config->value = $value;
        $config->save();
        Cache::forever($key, $value);
    }
}