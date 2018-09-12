<?php

namespace Zento\Kernel\Providers;

use Zento\Kernel\Booster\Events\EventsManager;

class EventsServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider {

    public function register() {
        $this->app->singleton('eventsmanager', function ($app) {
            return new EventsManager();
        });
    }
    
    /**
     * Get the events and handlers.
     *
     * @return array
     */
    public function listens() {
        return \Zento\Kernel\Facades\EventsManager::prepareEventListeners();
    }
}