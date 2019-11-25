<?php

namespace Zento\Kernel\Http;

class ApiResponse
{
  protected $data;

  public function __construct(array &$data) {
    $this->data = $data;
  }

  public function __get($key) {
    return $this->data[$key] ?? null;
  }

  public function __toString() {
    return json_encode($this->data);
  }

  public function getData($key) {
    if ($this->data['data'] ?? false) {
      return $this->data['data'][$key] ?? null;
    }
    return null;
  }
}
