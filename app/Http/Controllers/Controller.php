<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Classes\ApiReturn;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function callAction($method, $parameters)
    {
        $result = $this->{$method}(...array_values($parameters));

        $return = new ApiReturn($result);

        return $return->decorate();
    }
}
