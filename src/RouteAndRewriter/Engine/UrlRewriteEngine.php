<?php

namespace Zento\RouteAndRewriter\Engine;

use Illuminate\Support\Str;
use Zento\RouteAndRewriter\Model\UrlRewriteRule as RuleModel;
use Zento\RouteAndRewriter\Facades\RouteAndRewriterService;

class UrlRewriteEngine extends UrlRewriteEngineAbstract
{
    public function findRewriteRule(string $url) {
        return RuleModel::where('req_hash', md5($url))->first();
    }
}