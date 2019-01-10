<?php

namespace Zento\Kernel\Booster\Events;

class EventFiredResult
{
    protected $success = false;
    protected $data = [];
    
    public function __construct($success, array $data = []) {
        $this->success = true;
        $this->data = $data;
    }

    public function isSuccess() {
        return $this->success;
    }

    public function getData($key = null) {
        if (empty($key)) {
            return $this->data;
        } else {
            return $this->data[$key];
        }
    }

    public function addData($key, $value) {
        $this->data[$key] = $value;
    }

    public function toArray() {
        return ['success' => $this->success, 'data' => $this->data];
    }
}