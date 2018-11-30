<?php

namespace Zento\RouteAndRewriter\Providers;

use Request;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

use Zento\Kernel\Facades\PackageManager;
use Zento\RouteAndRewriter\Services\RouteAndRewriterService;

class RouteAndRewriterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('routeandrewriter_svc', function($app) {
            return new RouteAndRewriterService($app);
        });
        PackageManager::class_alias('\Zento\RouteAndRewriter\Facades\RouteAndRewriterService', 'RouteAndRewriterService');
    }

    public function boot() {
        if (!$this->app->runningInConsole()) {
            $this->app->booted(function ($app) {
                $app['routeandrewriter_svc']->appendRewriteEngine(new \Zento\RouteAndRewriter\Engine\UrlRewriteEngine());
            });
        }
    }
}