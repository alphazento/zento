<?php

namespace Zento\UrlRewriter\Http\Controllers;

use App\Http\Controllers\Controller;
use Zento\UrlRewriter\Model\UrlRewriteRule;

class RedirectController extends Controller
{
    public function redirect() {
        $rule = (UrlRewriteRule)(Registry::get('urlrewriterule'));
        return redirect($rule->to_uri, $rule->statusCode);
    }
}