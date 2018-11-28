<?php

namespace Zento\RouteAndRewriter\Engine;


interface UrlRewriteEngineInterface
{
    public function execute(\Illuminate\Http\Request $request);
    public function findRewriteRule(string $url);
}