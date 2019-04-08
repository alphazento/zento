<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DA\ORM\DynamicAttribute;

use Zento\Kernel\Facades\DanamicAttributeFactory;
use Zento\Kernel\Booster\Database\Eloquent\DA\ORM\DynamicAttribute;

class Single extends \Illuminate\Database\Eloquent\Model {
    public function isSingle() {
        return true;
    }

    public function __toString() {
        return $this->getMappedValue();
    }

    public function toArray() {
        return $this->getMappedValue();
    }

    protected function getMappedValue() {
        if (DanamicAttributeFactory::isWithMappedValue()) {
            if ($configs = DanamicAttributeFactory::getAttributeDesc($this->getTable())) {
                if (($configs['with_value_map'] ?? false) && ($configs['options'] ?? false)) {
                    return $configs['options'][$this->value];
                }
            }
        }
        return $this->value;
    }

    /**
     * @override function of \Illuminate\Database\Eloquent\Model
     *
     * @param  array  $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function newModelInstance($attributes = [])
    {
        return parent::newModelInstance($attributes)->setTable($this->getTable());
    }

    /**
     * @override function of \Illuminate\Database\Eloquent\Model
     *
     * @param array $attributes
     * @param [type] $connection
     * @return void
     */
    public function newFromBuilder($attributes = [], $connection = null) {
        return parent::newFromBuilder($attributes, $connection)->setTable($this->getTable());
    }

    /**
     * @override function of \Illuminate\Database\Eloquent\Model
     *
     * @param array $attributes
     * @param boolean $exists
     * @return void
     */
    public function newInstance($attributes = [], $exists = false)
    {
        return parent::newInstance($attributes, $exists)->setTable($this->getTable());
    }

    // public function getAttributeDesc() {
    //     $key = sprintf('%s.desc', $this->getTable());
    //     if (ShareBucket::has($key)) {
    //         return ShareBucket::get($key);
    //     }

    //     if (Cache::has($key)) {
    //         $configs = Cache::get($key);
    //         ShareBucket::put($key, $configs);
    //         return $configs;
    //     }
        
    //     $configs = [];
    //     if ($desc = DynamicAttribute::where('attribute_table', $this->getTable())
    //             ->first()) {
    //         $configs = $desc->toArray();
    //         if ($desc->with_value_map) {
    //             $rawOptions = $desc->options()->select(['value_id', 'value'])->get();
    //             $options = [];
    //             foreach($rawOptions as $option) {
    //                 $options[$option['value_id']] = $option['value'];
    //             }
    //             $configs['options'] = (count($options) > 0 ? $options : false);
    //         }
    //     }
    //     Cache::forever($key, $configs);
    //     ShareBucket::put($key, $configs);
    //     return $configs;
    // }
}
