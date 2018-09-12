<?php

namespace Zento\Kernel\Booster\Http;

class RewriteRequest extends \Illuminate\Http\Request {
    protected $rewriteTo;

    public function rewrite($path) {
        $this->rewriteTo = $path;
        return $this;
    }

    /**
    * @override
    */
    protected function prepareRequestUri()
    {
        $this->server->set('REQUEST_URI', $this->rewriteTo);
        return $this->rewriteTo;
    }
}