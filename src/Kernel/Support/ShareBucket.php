<?php
namespace Zento\Kernel\Support;

use Cache;

class ShareBucket 
{
    protected $bucket = [];
    public function get($key, $default=null) {
        if ($this->has($key)) {
            return $this->bucket[$key];
        }
        return $default;
    }

    public function has($key) {
        return isset($this->bucket[$key]);
    }

    public function put($key, $value) {
        $this->bucket[$key] = $value;
    }

    public function forget($key) {
        unset($this->bucket[$key]);
        return $this;
    }
}