<?php
namespace Zento\Kernel\Booster\Database\Eloquent\DA\ORM;

class DynamicAttributeSet extends \Illuminate\Database\Eloquent\Model {
    protected $fillable = [
        'name', 
        'description', 
        'model',
        'active'
    ];

    public function attributes() {
        return $this->HasManyThrough(DynamicAttribute::class, 
            DynamicAttributeInSet::class,
            'attribute_set_id', 'id', 'id', 'attribute_id');
    }

    public function defaultDynAttr($model) {
        $attrs = [];
        return [
            'name' => '',
            'description' => '' ,
            'model' => $model,
            'active' => false
        ];
    }
}