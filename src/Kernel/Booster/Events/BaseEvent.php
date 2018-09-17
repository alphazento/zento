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

    public function __construct($channelName = 'none') {
        $this->channelName = $channelName;
        $this->xRays = [];
    }

    /**
     * record listener for the event
     *
     * @param string $listener
     * @return $this
     */
    public function addXRay($listener) {
        $this->xRays[] = $listener;
        return $this;
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