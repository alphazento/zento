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
}