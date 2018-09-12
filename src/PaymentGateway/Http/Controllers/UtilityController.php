<?php

namespace Zento\Framework\PaymentGateway\Http\Controllers;

use Route;
use Request;
use App\Http\Controllers\Controller;

class UtilityController extends Controller {
    
    public function prepare() {
        return event(new \Zento\Framework\PaymentGateway\Events\PrePay(Request::all()), [], true);
    }

    public function cancel() {
        
    }

    public function postPay() {
        $data = Request::all();
        $data['validatehash'] = Route::input('validatehash');
        return event(new \Zento\Framework\PaymentGateway\Events\PostPay($data), [], true);
    }

    public function refund() {
        
    }
}