<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DA\ORM;

class DynamicAttribute extends \Illuminate\Database\Eloquent\Model {
    protected $fillable = [
        'parent_table', 
        'name', 
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
        'searchable',
        'search_layer_sort',
        'active'
    ];

    public function defaultDynAttr($parent_table) {
        return [
            'parent_table' => $parent_table, 
            'name' => '', 
            'attribute_table' => '', 
            'attribute_type' => '', 
            'label' => '',
            'admin_label' => '',
            'default_value' => '',
            'admin_group' => '',
            'admin_component' => '',
            'single' => true,
            'with_value_map' => false,
            'swatch_type' => '',
            'active' =>true,
            'searchable' => false,
            'search_layer_sort' => 999
        ];
    }

    public function options() {
        return $this->hasMany(DynamicAttributeValueMap::class, 'attribute_id', 'id');
    }
}
