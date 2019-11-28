<?php

namespace Zento\Kernel\Http\Controllers;

use Zento\Kernel\Consts;
use Zento\Kernel\Facades\ShareBucket;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ApiBaseController extends BaseController 
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests, TraitApiResponse;

  public function __construct() {
    ShareBucket::put(Consts::MODEL_RICH_MODE, true);
  }
}
