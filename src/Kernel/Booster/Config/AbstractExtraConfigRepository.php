<?php
namespace Zento\Kernel\Booster\Config;

abstract class AbstractExtraConfigRepository
{
    protected $groupingProvider;
    protected $hasFullloaded;
    protected $items;
    /**
     * key value items array
     */
    public function __construct(GroupingProviderInterface $groupingProvider) {
        $this->groupingProvider = $groupingProvider;
        $this->items = [];
    }

    /**
     * if all configs are loaded at the beginning ?
     *
     * @return boolean
     */
    public function isPreLoaded() {
        return $this->hasFullloaded;
    }

    /**
     * key value items array
     * @return feference 
     */
    public function &loadConfigs() {
        return $this->items;
    }

    /**
     * persist a key value pair
     */
    public function persist(string $key, $value, $groupName = NULL) {
        return $this;
    }

    /**
     * support wild card get
     */
    public function getManyLike(string $key, $groupName = NULL) {
        return [];
    }

    public function has(string $key, $groupName = NULL) {
        return false;
    }

    public function get(string $key, $groupName = NULL) {
        return null;
    }

    public function preGet(string $key, $groupName = NULL) {
        return $this;
    }

    public function set(string $key, string $value, $groupName = null) {
        return $this;
    }

}