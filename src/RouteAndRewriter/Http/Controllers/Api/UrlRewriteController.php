<?php

namespace Zento\RouteAndRewriter\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Zento\RouteAndRewriter\Facades\RouteAndRewriterService;

class UrlRewriteController extends Controller
{
    protected $recursive = 0;
    public function getUrlRewriteTo(\Illuminate\Http\Request $request) {
        if ($rule = $this->findRewriteRule($request->get('url'))) {
            if ($rule->statusCode == 301 || $rule->statusCode == 302) {

            }
            return ['status'=>200, 'data'=>['path' => $rule->to_uri, 'params' => $request->all()]];
        }
        return ['status'=>404, 'data'=>null];
    }

    protected function findRewriteRule($url) {
        if ($this->recursive > 5) {
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