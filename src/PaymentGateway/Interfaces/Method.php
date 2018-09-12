<?php
namespace Zento\Framework\PaymentGateway\Interfaces;

interface Method {
    public function getCode();

    public function getTitle();

    public function canOrder();

    public function canAuthorize();

    public function canCapture();

    public function canCapturePartial();

    public function canCaptureOnce();
    
    public function canRefund();

    public function canUseInternal();

    public function canUseCheckout();

    public function canEdit();

    public function canFetchTransactionInfo();

    public function canUseForCountry($country);

    public function canUseForCurrency($currencyCode);

    public function canReviewPayment();

    public function validate();

    public function order(InfoInterface $payment, $amount);

    public function authorize(InfoInterface $payment, $amount);

    public function capture(InfoInterface $payment, $amount);

    public function refund(InfoInterface $payment, $amount);

    public function acceptPayment(InfoInterface $payment);

    public function denyPayment(InfoInterface $payment);
}