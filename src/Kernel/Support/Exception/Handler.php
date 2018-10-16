<?php

namespace Zento\Kernel\Support\Exception;

use Exception;
use App\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Response;
use Zento\Kernel\Support\Varien\ProtocolMessage;
//composer require filp/whoops --dev

class Handler extends ExceptionHandler {
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if (class_exists('\Whoops\Run') && config('app.debug') && !$request->ajax()) {
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            return $whoops->handleException($e);
        }

        if ($e instanceof TokenMismatchException) {
            if ($request->ajax()) {
                return new Response(json_encode(['status'=>false, 'message' => 'token not match']), 500);
            } else {
                return redirect()->route('login');
            }
        }

        return parent::render($request, $e);
    }
}
