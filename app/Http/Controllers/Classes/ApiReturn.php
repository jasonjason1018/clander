<?php

namespace App\Http\Controllers\Classes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiReturn extends Controller
{
    const DEFAULT_MESSAGE = 'Success';
    const DEFAULT_CODE = '000';

    public $success = true;
    public $code = self::DEFAULT_CODE;
    public $message = self::DEFAULT_MESSAGE;
    public $result;

    public function __construct($result){
        $this->result = $result;
    }

    /**
     *
     */
    public function decorate(){
        unset($this->middleware);
        return json_encode((array)$this, JSON_UNESCAPED_UNICODE);
    }
}
