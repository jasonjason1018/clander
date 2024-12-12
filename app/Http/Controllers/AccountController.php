<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AccountService;

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
}
