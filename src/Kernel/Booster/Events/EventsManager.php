<?php

namespace Zento\Kernel\Booster\Events;

use Cache;
use Zento\Kernel\Consts;

class EventsManager {
    protected $rawEventListeners;
    protected $parsedEventListeners;
    protected $cached = false;

    public function __construct() {
        $this->rawEventListeners = $this->getRawListeners();
        if (Cache::has(Consts::CACHE_KEY_EVENTS_LISTENERS)) {
            $this->parsedEventListeners = Cache::get(Consts::CACHE_KEY_EVENTS_LISTENERS, null);
            $this->cached = !empty($this->parsedEventListeners);
        }
    }

    public function isCached() {
        return $this->cached;
    }

    /**
     * add evnets listeners
     *
     * @param array $rawEventListeners  [key(int)=>value(class name)]
     * @return void
     */
    public function addEventListeners(array $rawEventListeners) {
        foreach($rawEventListeners as $key => $values) {
            foreach($values as $sort => $value) {
                $sort_value = sprintf('%s:::%s', str_pad($sort, 5, "0", STR_PAD_LEFT), $value);
                $elements = isset($this->rawEventListeners[$key]) ? $this->rawEventListeners[$key] : [];
                $elements[] = $sort_value;;
                $this->rawEventListeners[$key] = $elements;
            }
        }
        Cache::forever(Consts::CACHE_KEY_RAW_EVENTS_LISTENERS, $this->rawEventListeners);
    }

    public function prepareEventListeners() {
        if (!$this->cached) {
            $this->parsedEventListeners = [];
            foreach($this->rawEventListeners ?? [] as $key => $listeners) {
                sort($listeners);
                $this->parsedEventListeners[$key] = array_map(function($v) {
                    $parts = explode(':::', $v);
                    return $parts[1];
                }, $listeners);
            }
            Cache::forever(Consts::CACHE_KEY_EVENTS_LISTENERS, $this->parsedEventListeners);
        }
        return $this->parsedEventListeners;
    }

    public function getRawListeners() {
        return Cache::get(Consts::CACHE_KEY_RAW_EVENTS_LISTENERS, []);
    }
}