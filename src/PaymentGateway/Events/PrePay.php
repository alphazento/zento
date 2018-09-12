<?php

namespace Zento\Framework\PaymentGateway\Events;

use Illuminate\Queue\SerializesModels;

class PrePay {
    use SerializesModels;
    public $data;

    /**
     * Create a new event instance.
     *
     * @param  array  $request data
     * @param  bool  $remember
     * @return void
     */
    public function __construct($data = null)
    {
        $this->data = $data;
    }


    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}