<?php

namespace Zento\RouteAndRewriter\Engine;

use Illuminate\Support\Str;
use Zento\RouteAndRewriter\Model\UrlRewriteRule as RuleModel;
use Zento\RouteAndRewriter\Facades\RouteAndRewriterService;

class UrlRewriteEngine implements UrlRewriteEngineInterface
{
    public function execute(\Illuminate\Http\Request $request) {
        /**
         * Not rewrite if not support api rewrite
         */
        if (! config('api_route_can_rewrite') && RouteAndRewriterService::isRequestsApi($request)) {
            return false;
        }
        
        return $this->_execute($request);
    }

    public function findRewriteRule(string $url) {
        return RuleModel::where('req_hash', md5($url))->first();
    }

    protected function _execute(\Illuminate\Http\Request $request) {
        if ($rule = $this->findRewriteRule(strtolower($request->path()))) {
            switch($rule->statusCode) {
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