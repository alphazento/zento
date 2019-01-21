<?php

namespace Zento\Kernel\Booster\Database\Eloquent\DA;

/**
 * a trait to handle product price
 * This trait turn relationship's properties to direct attributes of the host class by override function "toArray"
 * to use this trait, host class must define a function getPreloadRelations()
 *  e.g. 
 *  public function getPreloadRelations() {
 *       return [
 *         'relation1' =>[
 *             'mutator_attr1', 'mutator_attr2', 
 *          ],
 *          'relation2',
 *          'withcount' => ['relation1']
 *      ];
 *   }
 */
trait TraitRealationMutatorHelper {
    abstract public static function getPreloadRelations();

    protected $mutator_of_relation = false;
    protected function hasMutatorInRelations($key) {
        static $cache;
        if (!$cache) {
            $cache = [];
            foreach($this->getPreloadRelations() as $relation => $items) {
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
        $key_of_relation = $this->mutator_of_relation;
        if ($key_of_relation) {
            $relation = null;
            if (isset($this->relations[$key_of_relation])) {
                $relation = $this->relations[$key_of_relation];
            }
            if (!$relation) {
                $relation = $this->{$key_of_relation}()->getQuery()->getModel()->newInstance();
                $relation->{$relation->getForeignKeyName()} = $this->id;
                $this->relations[$key_of_relation] = $relation;
            }
            $relation->{$key} = $value;
            return $this;
        } else {
            return parent::setMutatedAttributeValue($key, $value);
        }
    }

    public function toArray() {
        $origin = parent::toArray();
        foreach($this->getPreloadRelations() as $relation => $items) {
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