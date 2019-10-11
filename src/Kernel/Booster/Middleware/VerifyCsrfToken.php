<?php

namespace Zento\Kernel\Booster\Middleware;

use Cookie;
use Zento\Kernel\Facades\ShareBucket;

class VerifyCsrfToken extends \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken
{
    protected function inExceptArray($request) {
        if (ShareBucket::has('ignore-csrf')) {
            return true;
        }
        return parent::inExceptArray($request);
    }

    /**
     * Get the CSRF token from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function getTokenFromRequest($request)
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');
        if (! $token && $header = $request->header('X-XSRF-TOKEN')) {
            $token = $this->encrypter->decrypt($header, static::serialized());
        }
        $token = $token ?? Cookie::get('XSRF-TOKEN');
        return $token;
    }
}
