<?php

namespace Zento\Kernel\ThemeManager\Middleware;

use Closure;
use Cookie;
use Zento\Kernel\Facades\ThemeManager;
use Zento\Kernel\ThemeManager\BrowserDetector;

class ThemeByBrowser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        ThemeManager::setTheme($this->detectTheme());
        return $next($request);
    }

    /**
     * detect wich theme to use for the browser
     */
    protected function detectTheme()
    {
        if ($theme = Cookie::get('theme')) {
            return $theme;
        }
        $detector = new BrowserDetector();
        if ($detector->isRobot()) {
            return 'phone';
        }
        if ($detector->isDesktop()) {
            return 'desktop';
        }
        if ($detector->isTablet()) {
            return 'tablet';
        }
        if ($detector->isPhone()) {
            return 'phone';
        }
        return 'desktop';
    }
}
