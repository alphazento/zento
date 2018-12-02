<?php

namespace Zento\RouteAndRewriter\Engine;
use Illuminate\Support\Str;


abstract class UrlRewriteEngineAbstract
{
    public function execute(\Illuminate\Http\Request $request) {
        $uri = strtolower($request->path());
        if (!Str::endsWith($uri, '.html')) {
            return false;
        }

        /**
         * Not rewrite if not support api rewrite
         */
        if (! config('api_route_can_rewrite') && RouteAndRewriterService::isRequestsApi($request)) {
            return false;
        }
        
        return $this->_execute($request);
    }

    abstract public function findRewriteRule(string $url);

    protected function _execute(\Illuminate\Http\Request $request) {
        if ($rule = $this->findRewriteRule(strtolower($request->path()))) {
            switch($rule->status_code) {
                case 200:
                    return \Zento\RouteAndRewriter\Model\RewriteRequest::capture()->rewrite($rule->to_uri);
                case 301:
                case 302:
                    Registory::put('urlrewriterule', $rule);
                    return \Zento\RouteAndRewriter\Model\RewriteRequest::capture()->rewrite('/redirect');
            }
        }
        return false;
    }
}