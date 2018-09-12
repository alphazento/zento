<?php

namespace Zento\Kernel\Booster\Events;

use Cache;
use Zento\Kernel\Consts;

class EventsManager {
    protected $eventListeners;
    protected $parsedEventListeners;
    protected $cached = false;

    public function __construct() {
        $this->eventListeners = [];
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
     * @param array $eventListeners  [key(int)=>value(class name)]
     * @return void
     */
    public function addEventListeners(array $eventListeners) {
        foreach($eventListeners as $key => $values) {
            foreach($values as $sort => $value) {
                $sort_value = sprintf('%s:::%s', str_pad($sort, 5, "0", STR_PAD_LEFT), $value);
                $elements = isset($this->eventListeners[$key]) ? $this->eventListeners[$key] : [];
                $elements[] = $sort_value;;
                $this->eventListeners[$key] = $elements;
            }
        }
    }

    public function addSubscriber($subscriber) {
        $this->subscribe[] = $subscriber;
    }

    public function prepareEventListeners() {
        if (!$this->cached) {
            $this->parsedEventListeners = [];
            foreach($this->eventListeners as $key => $listeners) {
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
}