<?php

namespace Zento\Kernel\Http\Controllers;
use Zento\Kernel\Http\ApiResponse;

trait TraitApiResponse
{
  protected $data = [
    'success' => true,
    'code' => 200,
    'locale' => 'en',
    'message' => '',
    'data' => []
  ];

  protected function response($data) {
    if (is_object($data)) {
      $data = $data->toArray();
    } 
    if (is_array($data)) {
      $this->data = array_merge($this->data, $data);
    }
    return $this;
  }

  protected function success($code = 200, $message = null) {
    $this->data['success'] = true;
    $this->data['code'] = $code;
    if ($message !== null) {
      $this->data['message'] = $message;
    }
    return $this;
  }

  protected function error($code = 400, $message = null) {
    $this->data['success'] = false;
    $this->data['code'] = $code;
    if ($message !== null) {
      $this->data['message'] = $message;
    }
    return $this;
  }

  protected function with($key, $value) {
    $this->data[$key] = $value;
    return $this;
  }

  protected function withData($data) {
    if (is_object($data)) {
      $this->data['data'] = $data;
    } else {
      if (is_array($data)) {
        $this->data['data'] = array_merge($this->data['data'], $data);
      }
    }
    
    return $this;
  }

  public function __toString() {
    return json_encode($this->data);
  }

  public function getApiResponse() : \Zento\Kernel\Http\ApiResponse {
    return new ApiResponse($this->data);
  }
}
