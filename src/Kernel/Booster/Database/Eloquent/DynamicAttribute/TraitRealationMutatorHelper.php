<?php

namespace Zento\Zento\Model\ORM\Traits;

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