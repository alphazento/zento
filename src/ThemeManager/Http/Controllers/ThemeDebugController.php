<?php

namespace Zento\ThemeManager\Http\Controllers;

use Request;
use Session;
use App\Http\Controllers\Controller;

class ThemeDebugController extends Controller
{
    public function index() {
        return view('debugiframe');
    }

    public function post() {
        $config = Request::all();
        unset($config['_token']);
        if ($config['block-debug-hint']) {
            Session::put('block-debug-hint', true);
        } else {
            Session::forget('block-debug-hint');
        }
        unset($config['block-debug-hint']);
        Session::put('s3theme-config', $config);
        return view('debugiframe');
    }
}