<?php

namespace Zento\Framework\PaymentGateway\Providers;

use Zento\Framework\PaymentGateway\Services\Payment;
use Illuminate\Support\ServiceProvider;

class Entry extends ServiceProvider
{
    const CUSTOMER_DATA_CAPSULE_KEY = '_customer_data_';
    const CUSTOMER_DATA_LIFE_TIME = '7200';

    public function register()
    {
        $this->app->singleton('payment', function ($app) {
            return new Payment($app);
        });

        class_alias('\Zento\Framework\PaymentGateway\Providers\Facades\Payment', 'Payment');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['payment'];
    }
}
