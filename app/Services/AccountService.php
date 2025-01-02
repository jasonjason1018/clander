<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\Account;
use App\Facades\Tool;

class AccountService
{
    public function validateAccountParam($requestData)
    {
        $rule = [
            'name' => 'required | string',
            'email' => 'required | string',
            'password' => 'required | string',
        ];

        return Validator::make($requestData, $rule);
    }

    public function checkHasEmail($email)
    {
        return Account::where('email', '=', $email)->exists();
    }

    public function register($name, $email, $password, $note)
    {
        return Account::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'note' => $note,
        ]);
    }

    public function validateAccount($email, $password)
    {
        $account = Account::where('email', '=', $email)->first();

        if ($account['password'] != $password) {
            throw new \Exception('Invalid email or password.');
        }

        if ($account['status'] == Account::ACCOUNT_STATUS['deactivate']) {
            throw new \Exception('Account is deactivated');
        }

        return $account;
    }

    public function getAccessToken($account)
    {
        $data = [
            'id_account' => $account->id_account,
            'expired_time' => Carbon::now()->addMinutes(15)->format('Y-m-d H:i:s'),
        ];

        $data["time"] = Carbon::now()->getPreciseTimestamp(6);

        $json = stripslashes(json_encode($data));
        $encrypt = Tool::base64UrlEncode(Tool::encryptExtra($json));

        $result['access_token'] = $encrypt;
        $result['expired_time'] = $data['expired_time'];

        return $result;
    }

    public function getTokenInfo($accessToken)
    {
        return json_decode(Tool::decryptExtra(Tool::base64UrlDecode($accessToken)), true);
    }

    public function getAccountInfo($idAccount)
    {
        return Account::find($idAccount);
    }

    public function updateAccountInfo($idAccount, $updateData)
    {
        return Account::where('id_account', '=', $idAccount)
            ->update($updateData);
    }
}
