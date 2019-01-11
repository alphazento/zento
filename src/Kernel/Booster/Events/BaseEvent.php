<?php

namespace Zento\Kernel\Booster\Events;

use Illuminate\Broadcasting\PrivateChannel;

class BaseEvent
{
    use \Zento\Kernel\Support\Traits\TraitLogger;
    use \Illuminate\Queue\SerializesModels;

    const HAS_ATTRS = [];
    protected $data = [];
    protected $channelName;

    /**
     * to tell this event has been handled by listeners
     *
     * @var array
     */
    protected $xRays = [];

    protected $errors = [];

    public function __get($attr) {
        if (in_array($attr, static::HAS_ATTRS)) {
            return isset($this->data[$attr]) ? $this->data[$attr] : null;
        }
        throw new \Exception(sprintf('%s is not a data item for the event %s', $attr, static::class));
    }

    public function __set($attr, $value) {
        throw new \Exception(sprintf('attribute %s is not allow to set directly for event %s', $attr, static::class));
    }

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
        $this->xRays[] = $listener;
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
    public function fireUntil($payload = []) : \Zento\Kernel\Booster\Events\EventFiredResult {
        return $this->fire($payload, true);
    }

    /**
     * fire event
     *
     * @param array $payload
     * @return void
     */
    public function fire($payload = [], $halt = false) : \Zento\Kernel\Booster\Events\EventFiredResult
    {
        $result = event($this, $payload, $halt);
        $this->debug('event', ['xray' => $this->xRays, 'result' => $result]);
        if ($result === null) {
            return $this->createResult(true, ['message'=> 'No listener for the event has a return result']);
        }
        return $result;
    }

    public function createResult($success, array $data=[]) : \Zento\Kernel\Booster\Events\EventFiredResult 
    {
        return new \Zento\Kernel\Booster\Events\EventFiredResult($success, $data);
    }
}