<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DA\ORM;

class DynamicAttribute extends \Illuminate\Database\Eloquent\Model {
    protected $fillable = [
        'parent_table', 
        'attribute_name', 
        'label',
        'admin_label',
        'admin_group',
        'admin_component',
        'attribute_table', 
        'attribute_type', 
        'default_value',
        'single',
        'with_value_map',
        'swatch_type',
        'is_search_layer',
        'search_layer_sort',
        'enabled'
    ];

    public function defaultDynAttr($parent_table) {
        $attrs = [];
        foreach($this->fillable as $attr) {
            $attrs[$attr] = null;
        }
        $attrs['parent_table'] = $parent_table;
        $attrs['single'] = true;
        $attrs['with_value_map'] = false;
        $attrs['swatch_type'] = false;
        $attrs['is_search_layer'] = false;
        $attrs['search_layer_sort'] = 999;
        $attrs['enabled'] = true;
        return $attrs;
    }

    public function options() {
        return $this->hasMany(DynamicAttributeValueMap::class, 'attribute_id', 'id');
    }
}
