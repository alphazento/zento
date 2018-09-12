<?php

namespace Zento\UrlRewriter\Illuminate\Http;

class RewriteRequest extends \Illuminate\Http\Request {
    protected $rewriteTo;

    /**
     * rewrite to url
     */
    public function rewrite($uri) {
        $this->rewriteTo = $uri;
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