<?php

namespace Zento\Kernel\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ApiBaseController extends BaseController 
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests, TraitApiResponse;
}
