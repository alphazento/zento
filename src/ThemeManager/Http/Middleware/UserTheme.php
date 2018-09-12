<?php

namespace Zento\ThemeManager\Http\Middleware;

use Closure;

use Auth;
use Session;
use Cookie;
use ThemeManager;

class UserTheme
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (Cookie::get('theme')) {
            ThemeManager::prependUserThemeLocation(Cookie::get('theme'));
        }

        return $next($request);
    }
}