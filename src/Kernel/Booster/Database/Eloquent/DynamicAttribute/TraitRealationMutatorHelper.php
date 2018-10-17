<?php

namespace Zento\Kernel\Booster\Database\Eloquent\DynamicAttribute;

/**
 * a trait to handle product price
 * This trait turn relationship's properties to direct attributes of the host class by override function "toArray"
 * to use this trait, host class must define a static property: $preload_relations
 *  e.g. public static $preload_relations" =
     * [
        'relation1' =>[
            'mutator_attr1', 'mutator_attr2', 
        ],
        'relation2',
        'withcount' => ['relation1']
    ];
 */
trait TraitRealationMutatorHelper {
    protected $mutator_of_relation = false;

    protected function hasMutatorInRelations($key) {
        static $cache;
        if (!$cache) {
            $cache = [];
            foreach(static::$preload_relations as $relation => $items) {
                if ($relation !== 'withcount') {
                    if (is_numeric($relation) && is_string($items)) {
                        continue;
                    }
                    if (is_array($items)) {
                        foreach($items as $mutator) {
                            $cache[$mutator] = $relation;
                        }
                    }
                }
            }
        }

        $this->mutator_of_relation = isset($cache[$key]) ? $cache[$key] : false;
        return $this->mutator_of_relation;
    }

    /**
     * @override
     */
    public function hasGetMutator($key)
    {
        return parent::hasGetMutator($key) || $this->hasMutatorInRelations($key);
    }

    /**
     * @override
     */
    protected function mutateAttribute($key, $value)
    {
        if ($this->mutator_of_relation) {
            $relation = $this->relations[$this->mutator_of_relation];
            return $relation ? $relation->{$key} : null;
        } else {
            return parent::mutateAttribute($key, $value);
        }
    }

    /**
     * @override
     */
    public function hasSetMutator($key)
    {
        return parent::hasSetMutator($key) || $this->hasMutatorInRelations($key);
    }

    /**
     * @override
     */
    protected function setMutatedAttributeValue($key, $value)
    {
        if ($this->mutator_of_relation) {
            $relation = $this->relations[$this->mutator_of_relation];
            if (!$relation) {
                $relation = $this->{$this->mutator_of_relation}()->getQuery()->getModel()->newInstance();
                $this->relations[$this->mutator_of_relation] = $relation;
            }
            $this->relations[$this->mutator_of_relation]->{$key} = $value;
            return $this;
        } else {
            return parent::setMutatedAttributeValue($key, $value);
        }
    }

    public function toArray() {
        $origin = parent::toArray();
        foreach(static::$preload_relations as $relation => $items) {
            if ($relation !== 'withcount') {
                if (is_numeric($relation) && is_string($items)) {
                    continue;
                }
                if (is_array($items)) {
                    foreach($items as $mutator) {
                        $origin[$mutator] = $this->{$mutator};
                    }
                    unset($origin[$relation]);
                }
            }
        }
        return $origin;
    }
}