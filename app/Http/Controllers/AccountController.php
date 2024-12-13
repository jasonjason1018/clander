<?php

namespace App\Http\Controllers;

use Facade\Ignition\Support\Packagist\Package;
use Illuminate\Http\Request;
use App\Services\AccountService;
use Illuminate\Support\Facades\Redis;

class AccountController extends Controller
{
    public function register(Request $request)
    {
        $accountService = new AccountService();

        $validate = $accountService->validateAccountParam($request->all());

        if ($validate->fails()) {
            throw new \Exception('Invalid request parameters.');
        }

        $name = $request->input('name');
        $email = $request->input('email');

        //驗證email格式
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Invalid email address.');
        }

        $hasEmail = $accountService->checkHasEmail($email);

        if ($hasEmail) {
            throw new \Exception('Email already in use.');
        }

        $password = $request->input('password');
        $note = $request->input('note', null);

        return $accountService->register($name, $email, $password, $note);
    }

    public function login(Request $request)
    {
        $email = $request->input('email', '');

        if (!$email) {
            throw new \Exception('Email cannot be empty.');
        }

        $password = $request->input('password', '');

        if (!$password) {
            throw new \Exception('Password cannot be empty.');
        }

        $accountService = new AccountService();

        $account = $accountService->validateAccount($email, $password);

        $result = $accountService->getAccessToken($account);

        $this->setRedis($result['access_token']);

        return $result;
    }
}
