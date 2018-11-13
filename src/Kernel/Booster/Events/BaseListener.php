<?php

namespace Zento\Kernel\Booster\Events;

class BaseListener
{
    use \Zento\Kernel\Support\Traits\TraitLogger;
    
    public function handle($event) {
        $event->addXRay(static::class);
        try {
            return $this->run($event);
        } catch (\Exception $e) {
            $event->addError($e);
            return $e;
        }
    }

    protected function run($event) {
        throw new ListenerLogicNotDefine('Listener Logic Not Defined');
    }
}