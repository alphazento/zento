<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DA\ORM;

class DynamicAttribute extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = [
        'parent_table',
        'name',
        'front_label',
        'front_component',
        'front_group',
        'admin_label',
        'admin_group',
        'admin_component',
        'attribute_table',
        'attribute_type',
        'default_value',
        'single',
        'with_value_map',
        'use_container',
        'searchable',
        'sort',
        'active',
    ];

    public function defaultDynAttr($parent_table)
    {
        return [
            'parent_table' => $parent_table,
            'name' => '',
            'attribute_table' => '',
            'attribute_type' => '',
            'front_label' => '',
            'front_component' => '',
            'front_group' => '',
            'admin_label' => '',
            'default_value' => '',
            'admin_group' => '',
            'admin_component' => '',
            'single' => true,
            'with_value_map' => false,
            'use_container' => 0,
            'active' => true,
            'searchable' => false,
            'sort' => 999,
        ];
    }

    public function options()
    {
        return $this->hasMany(DynamicAttributeValueMap::class, 'attribute_id', 'id');
    }

    public function sets()
    {
        return $this->HasManyThrough(DynamicAttributeSet::class,
            DynamicAttributeInSet::class,
            'attribute_id', 'id', 'id', 'attribute_set_id');
    }
}
