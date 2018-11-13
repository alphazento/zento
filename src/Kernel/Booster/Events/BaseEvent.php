<?php

namespace Zento\Kernel\Booster\Events;

use Illuminate\Broadcasting\PrivateChannel;

class BaseEvent
{
    use \Zento\Kernel\Support\Traits\TraitLogger;
    use \Illuminate\Queue\SerializesModels;

    /**
     * to tell this event has been handled by listeners
     *
     * @var array
     */
    protected $xRays;

    protected $channelName;

    protected $errors = [];

    /**
     * set channel if going to use channel to broadcast
     *
     * @param string $channelName
     * @return void
     */
    public function setChannel($channelName = 'none') {
        $this->channelName = $channelName;
    }

    /**
     * record listener for the event
     *
     * @param string $listener
     * @return $this
     */
    public function addXRay($listener) {
        if ($this->xRays == null) {
            $this->xRays = [$listener];
        } else {
            $this->xRays[] = $listener;
        }
        return $this;
    }

    public function addError($errorMessage) {
        $this->errors[] = $errorMessage;
    }

    public function getErrors() {
        return $this->errors;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn() {
        return new PrivateChannel($this->channelName);
    }

    /**
     * fire event
     *
     * @param array $payload
     * @return void
     */
    public function fireUntil($payload = []) {
        return $this->fire($payload, true);
    }

    /**
     * fire event
     *
     * @param array $payload
     * @return void
     */
    public function fire($payload = [], $halt = false) {
        $result = event($this, $payload, $halt);
        $this->debug('event', ['xray' => $this->xRays, 'result' => $result]);
        return $result;
    }
}