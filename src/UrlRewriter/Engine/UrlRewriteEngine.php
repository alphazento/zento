<?php

namespace Zento\UrlRewriter\Engine;

use Illuminate\Support\Str;
use Zento\UrlRewriter\Model\UrlRewriteRule as RuleModel;

class UrlRewriteEngine
{
    public function execute(\Illuminate\Http\Request $request) {
        $rule = RuleModel::where('req_hash', md5(strtolower($request->path())))->first();
        if ($rule) {
            switch($rule->statusCode) {
                case 200:
                    return \Zento\UrlRewriter\Model\RewriteRequest::capture()->rewrite($rule->to_uri);
                case 301:
                case 302:
                    Registory::put('urlrewriterule', $rule);
                    return \Zento\UrlRewriter\Model\RewriteRequest::capture()->rewrite('/redirect');
            }
        }
        return false;
    }
}