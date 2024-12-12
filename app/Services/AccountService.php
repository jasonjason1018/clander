<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use App\Models\Account;

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
}
