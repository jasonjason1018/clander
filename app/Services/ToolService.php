<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class ToolService
{
    private $key;
    private $method;
    private $iv;

    public function __construct()
    {
        $this->key = Config::get('app.api_extra_cipher_key');
        $this->method = Config::get('app.api_extra_cipher_method');
        $this->iv = Config::get('app.api_extra_cipher_iv');
    }

    public function encryptExtra($data)
    {
        return openssl_encrypt($data, $this->method, $this->key, 0, $this->iv);
    }

    /**
     * 對外解密服務的方法。
     */
    public function decryptExtra($data)
    {
        return openssl_decrypt($data, $this->method, $this->key, 0, $this->iv);
    }

    public function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function base64UrlDecode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
