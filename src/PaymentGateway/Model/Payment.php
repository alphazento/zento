<?php

namespace Zento\Framework\PaymentGateway\Model;
use Zento\Framework\Legacy\Model\ZentoTable;

use Carbon\Carbon;

class Payment extends ZentoTable {
    protected $primaryKey = 'id';
    protected $table = 'payments';
    public $timestamps = false;
    
    public function order() {
        // return $this->hasMany()
    }

    public function customer() {
        // return $this->hasOne()
    }

    public function adminuser() {

    }

    public function method() {

    }

    public static function logPayment($order, $paymentStatus) {
        $payment = new static();
        $payment->customer_id = $order->customers_id;
        $payment->payment_type_id = 2;
        $payment->transaction_id = $order->transactionID;
        $payment->payment_status_id = $paymentStatus;
        $payment->unapplied = '0.00';
        $payment->payment_date = Carbon::now();
        $payment->amount = $order->currency_value;
        $payment->ref_id = $order->orders_id;
        $payment->admin_id = 0;
        $payment->admin_tmp_id = 0;
        $payment->created = Carbon::now();
        $payment->modified = Carbon::now();
        $payment->ns_tran = Carbon::now();
        $payment->save();
        return $payment;
    }
}