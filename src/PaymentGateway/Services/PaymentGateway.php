<?php

namespace Zento\Framework\PaymentGateway\Services;

use Config;
use Registry;
use Closure;
use Zento\Framework\Foundation\Contract\Service\PaymentMethod;

class PaymentGateway {
    const METHOD_WILD_CARD = 'paymentmethod.%s.service';

    protected $app;

    public function __construct($app) {
        $this->app = $app ?: app();
    }

    public function all() {
        $methods = Config::getManyLike(sprintf(self::METHOD_WILD_CARD, '%'));
        return array_values($methods);
    }

    public function getService($serviceName) {
        return $this->app[$name];
    }

    public function allAvailables($quoteId, $user, $shippingAddress) {
        $serviceNames = $this->all();
        $availables = [];
        foreach($serviceNames as $serviceName) {
            $service = $this->getService($serviceName);
            if ($service->isAvailable($quoteId, $user, $shippingAddress)) {
                $availables[] = $serviceName;
            }
        }
        return $availables;
    }

    public function registerMethod($methodName, $serviceName) {
        return Config::persist(sprintf(self::METHOD_WILD_CARD, $methodName), $serviceName);
    }

    public function enableMethod($serviceName) {
        $service = $this->getService($serviceName);
        $service->enable();
    }

    public function disableMethod($serviceName) {
        $service = $this->getService($serviceName);
        $service->disable();
    }

    public function renderMethodForm($serviceName) {
        $service = $this->getService($serviceName);
        return $service->renderMethodForm();
    }
}