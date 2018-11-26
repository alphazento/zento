<?php

namespace Zento\RouteAndRewriter\Providers;

use Request;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use Zento\RouteAndRewriter\Services\UrlRewriterManagerService;

class RouteAndRewriterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('urlrewriter', function($app) {
            return new UrlRewriterManagerService($app);
        });
    }
    public function boot() {
        if(!$this->app->runningInConsole()) {
            $this->app['urlrewriter']->appendRewriteEngine(function($request) {
                $engine = new \Zento\RouteAndRewriter\Engine\UrlRewriteEngine();
                $engine->execute($request);
            });
        }
    }
}