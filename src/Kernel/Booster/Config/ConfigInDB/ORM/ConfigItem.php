<?php
namespace Zento\Kernel\Booster\Config\ConfigInDB\ORM;

class ConfigItem extends \Illuminate\Database\Eloquent\Model
{
    /**
     * unserialize rawvalue
     *
     * @param  string  $value
     * @return string
     */
    public function getValueAttribute($value)
    {
        return unserialize($value);
    }

    /**
     * unserialize rawvalue
     *
     * @param  string  $value
     * @return string
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = serialize($value);
    }
}
