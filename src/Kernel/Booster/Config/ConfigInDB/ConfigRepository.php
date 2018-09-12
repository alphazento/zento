<?php
namespace Zento\Kernel\Booster\Config\ConfigInDB;

use Illuminate\Support\Arr;
use Zento\Kernel\Booster\Config\AbstractExtraConfigRepository;
use Zento\Kernel\Booster\Config\ConfigInDB\GroupingProvider;
use Zento\Kernel\Booster\Config\ConfigInDB\ORMModel\ConfigItem;

class ConfigRepository extends AbstractExtraConfigRepository
{
    /**
     * get a config value
     *
     * @param string $key
     * @param mixed $default value when key's not found
     * @param string $groupName
     * @return void
     */
    public function get(string $key, $default = null, $groupName = null) {
        $item = $this->getItem($key, $groupName);
        if ($item) {
            $this->items[$key] = $item->value;
            return $item->value;
        }

        $collection = $this->getManyLike($key, $groupName);
        foreach ($collection ?? [] as $item) {
            $this->items[$item['key']] = $item['value'];
        }
    }

    /**
     * get a config value
     *
     * @param string $key
     * @param mixed $default value when key's not found
     * @param string $groupName
     * @return void
     */
    public function preGet(string $key, $default = null, $groupName = null) {
        $item = $this->getItem($key, $groupName);
        if ($item) {
            $this->items[$key] = $item->value;
        } else {
            $collection = $this->getManyLike($key, $groupName);
            if (!empty($collection)) {
                foreach ($collection as $item) {
                    $this->items[$item->key] = $item->value;
                }
            } else {
                $this->items[$key] = $default;
            }
        }
        return $this;
    }


    public function has(string $key, $default = null, $groupName = null) {
        return $this->get($key, $default, $groupName);
    }

    /**
     * persist a key value pair
     * @param string $key
     * @param mixed $default value when key's not found
     * @param string $groupName
     */
    public function persist(string $key, $value, $groupName = null) {
        $groupName = $groupName ?? $this->groupingProvider->groupName();

        $item = $this->getItem($key, $groupName) ?? new ConfigItem();

        if ($item) {
            $item->group = $groupName;
            $item->key = $key;
            $item->value = $value;
            $item->save();
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $key
     * @param string $groupName
     * @return void
     */
    protected function getItem(string $key, $groupName = null) {
        $groupName = $groupName ?? $this->groupingProvider->groupName();
        $groups = array_unique([$groupName, GroupingProvider::DEFAULT_GROUP]);
        return ConfigItem::where('key', $key)
                ->whereIn('group', $groups)
                ->orderByRaw(sprintf("FIELD(`group`,'%s') ASC", implode("','", $groups)))
                ->first();
    }

    /**
     * support wild card get
     */
    public function getManyLike(string $key, $groupName = null) {
        $groupName = $groupName ?? $this->groupingProvider->groupName();
        $groups = array_unique([$groupName, GroupingProvider::DEFAULT_GROUP]);
        return ConfigItem::where('key', 'like',  $key . '.%')
                ->whereIn('group', $groups)
                ->orderByRaw(sprintf("FIELD(`group`, '%s') ASC", implode("','", $groups)))
                ->select(['key', 'value'])
                ->get();
    }
}