<?php

namespace Zento\Kernel\Booster\Events;

class BaseObserver
{
    use \Zento\Kernel\Support\Traits\TraitLogger;
    public function handle($event) {
        try {
            $result = $this->run($event);
            $this->info(
                    'e', 
                    [ 
                        'session'=>Session::getId(), 
                        'ret' => $result
                    ]
            );
            return $result;
        } catch (\Exception $e) {
            $this->error(
                'e',
                [
                    'session' => Session::getId(), 
                    'ret' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]
            );
        }
    }
}