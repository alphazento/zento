<?php

namespace Zento\Kernel\Booster\Mixins\Routing;

class Route {
    public function unshiftMiddleware() {
        return function(string $middleware) {
            $this->action['middleware'] = $this->action['middleware'] ?? [];
            array_unshift($this->action['middleware'], $middleware);
            return $this;
        };
    }

    public function catalog() {
        return function() {
            return $this->action['catalog'] ?? 'unknow';
        };
    }

    public function scope() {
        return function() {
            return $this->action['scope'] ?? 'unknow';
        };
    }
}