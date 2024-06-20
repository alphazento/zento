<?php

namespace Zento\RouteAndRewriter\Http\Controllers;

use App\Http\Controllers\Controller;
use ShareBucket;

class RedirectController extends Controller
{
    public function redirect()
    {
        if ($rule = ShareBucket::get('urlrewriterule')) {
            return redirect($rule->to_uri, $rule->status_code);
        }
        echo 'ERRRRR' . PHP_EOL;
        return 'error';
    }
}
