<?php

namespace Zento\Kernel\Booster\Mixins\Routing;

class UrlGenerator {
    public function setAssetRoot() {
        return function($assetRoot) {
            $this->assetRoot = $assetRoot;
            return $this;
        };
    }
}