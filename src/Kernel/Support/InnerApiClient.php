<?php
namespace Zento\Kernel\Support;

use Route;
use Request;

class InnerApiClient
{
    public function get($url) {
        $request = Request::create($url, 'GET');
        return $this->req($request);
    }

    public function post($url, $data = []) {
        $request = Request::create($url, 'POST');
        $request->replace($data);
        return $this->req($request);
    }

    public function delete($url) {
        $request = Request::create($url, 'DELETE');
        return $this->req($request);
    }

    public function patch($url) {
        $request = Request::create($url, 'PATCH');
        return $this->req($request);
    }

    private function req($request) {
        $originReq = Request::instance();
        app()->instance('middleware.disable', true);
        app()->instance('request', $request);
        $resp = Route::dispatch($request);
        $respData = $resp->getOriginalContent();
        $respData['data'] = $respData['data'] ? $respData['data']->toArray() : null;
        app()->instance('request', $originReq);
        return $respData;
    }
}