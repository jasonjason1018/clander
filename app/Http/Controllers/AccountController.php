<?php

namespace App\Http\Controllers;

use Facade\Ignition\Support\Packagist\Package;
use Illuminate\Http\Request;
use App\Services\AccountService;
use App\Models\Account;

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

    public function refreshToken(Request $request)
    {
        $accessToken = $request->input('access_token', '');

        if (!$accessToken) {
            throw new \Exception('AccessToken cannot be empty.');
        }

        $accountService = new AccountService();

        $accountInfo = $accountService->getTokenInfo($accessToken);

        if (!isset($accountInfo['id_account'])) {
            throw new \Exception('Invalid access token.');
        }

        $account = Account::find($accountInfo['id_account']);

        if (!$account) {
            throw new \Exception('Account not found.');
        }

        $result = $accountService->getAccessToken($account);

        $this->delRedis($accessToken);
        $this->setRedis($result['access_token']);

        return $result;
    }

    public function logout(Request $request)
    {
        $accessToken = $request->input('access_token', '');

        if (!$accessToken) {
            throw new \Exception('AccessToken cannot be empty.');
        }

        $this->delRedis($accessToken);

        return true;
    }

    public function getAccountInfo(Request $request)
    {
        $idAccount = $request->input('id_account', '');

        if (!$idAccount) {
            throw new \Exception('id_account cannot be empty.');
        }

        $accountService = new AccountService();

        return $accountService->getAccountInfo($idAccount);
    }
}
