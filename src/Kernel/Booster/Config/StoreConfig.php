<?php

namespace Zento\Kernel\Booster\Config;

use ShareBucket;
use Zento\Kernel\Booster\Config\ConfigInDB\ORM\ConfigItem;

class StoreConfig
{
    const CACHE_PATH = 'bootstrap/cache/zento_configs.php';

    public function __construct()
    {
        $this->attach();
    }

    protected function attach()
    {
        $cacheFile = base_path(static::CACHE_PATH);
        $data = [];
        if (false === stream_resolve_include_path($cacheFile)) {
            $store_id = ShareBucket::get('app.store_id', config('app.store_id', 1));
            if ($collection = ConfigItem::whereIn('store_id', [0, $store_id])
                ->orderBy('store_id', 'desc')
                ->orderBy('key')
                ->get()) {
                foreach ($collection as $item) {
                    data_fill($data, $item->key, $item->value);
                }
            }
            try {
                file_put_contents($cacheFile, '<?php return ' . var_export($data, true) . ';');
            } catch (\Throwable $e) {
            }
        } else {
            $data = require $cacheFile;
        }

        $configService = app('config');
        foreach ($data as $key => $value) {
            $config = $configService->get($key, []);
            $configService->set($key, array_merge(is_array($value) ? $value : [$value], $config));
        }
    }

    /**
     * Retrive config value without cache
     * @param $key string
     * @param $defaultValue
     * @param $store_id
     */
    public function get($key, $defaultValue = null, $store_id = 0)
    {
        if ($item = ConfigItem::where('store_id', '=', $store_id)
            ->where('key', $key)
            ->first()) {
            return $item->value;
        }
        return $defaultValue;
    }

    /**
     * Store kv in DB
     * @param $key string
     * @param $defaultValue
     * @param $store_id
     */
    public function set($key, $value, $store_id = 0)
    {
        $item = ConfigItem::where('store_id', '=', $store_id)
            ->where('key', $key)
            ->first() ?? new ConfigItem();
        $item->key = $key;
        $item->value = $value;
        $item->store_id = $store_id;
        $item->save();
    }
}
