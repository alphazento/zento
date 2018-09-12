<?php
Route::group(
    [
        'prefix' => '/payment/',
        'namespace' => '\Zento\Framework\PaymentGateway\Http\Controllers',
        'middleware' => ['web', 'mustlogin']
    ], function () {
        Route::post(
            '/prepay', 
            ['as'=>'payment.prepay', 'uses'=>'UtilityController@prePay']
        );
        Route::post(
            '/cancelpay', 
            ['as'=>'payment.cancelpay', 'uses'=>'UtilityController@cancelPay']
        );

        Route::post(
            '/touchLock', 
            ['as'=>'payment.touchLock', 'uses'=>'UtilityController@keepCheckoutLock']
        );
});

Route::group(
    [
        'prefix' => '/payment/',
        'namespace' => '\Zento\Framework\PaymentGateway\Http\Controllers',
        'middleware' => ['web']
    ], function() {
        Route::post(
            '/postpay', 
            ['as'=>'payment.postpay', 'uses'=>'UtilityController@postPay']
        );

        Route::get(
            '/postpay/{quote_id}/{validatehash}', 
            ['as'=>'payment.postpay.get', 'uses'=>'UtilityController@postPay']
        );
});