<?php

namespace Zento\RouteAndRewriter\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Zento\RouteAndRewriter\Facades\RouteAndRewriterService;

class UrlRewriteController extends Controller
{
    protected $recursive_level = 0;
    public function getUrlRewriteTo(\Illuminate\Http\Request $request) {
        if ($rule = $this->recursiveFindRewriteRule($request->get('url'))) {
            return ['status'=>200, 'data'=>['path' => $rule->to_uri, 'params' => $request->all()]];
        }
        return ['status'=>404, 'data'=>null];
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @return RewriteRule|false
     */
    protected function recursiveFindRewriteRule(string $url) {
        if ($this->recursive_level++ > 5) {
            return false;
        }
        if ($rule = RouteAndRewriterService::findRewriteRule($url)) {
            if ($rule->statusCode == 200) {
                return $rule;
            } elseif ($rule->statusCode == 301 || $rule->statusCode == 302) {
                return $this->findRewriteRule($rule->to_uri);
            }
        }
        return false;
    }
}