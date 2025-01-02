<?php

namespace Tests\Feature\Controllers;

use App\Services\AccountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;
use App\Models\Account;
use Illuminate\Support\Carbon;

class AccountControllerTest extends TestCase
{
    protected $seeders = [
        \AccountSeeder::class,
    ];
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCreateAccount()
    {
        $params = [
            'name' => 'admin1',
            'email' => 'admin1@xx.x',
            'password' => 'admin1',
        ];

        $response = $this->post('/api/account/register', $params);
        $response->assertStatus(200);

        $this->assertEquals($params['name'], $response->json()['result']['name']);
        $this->assertEquals($params['email'], $response->json()['result']['email']);

        $params = [
            'name' => 'admin1',
            'email' => 'admin1@xx.x',
            'password' => 'admin1',
        ];

        $response = $this->call('POST', '/api/account/register', $params);
        $response->assertStatus(500);

        $expect = 'Email already in use.';
        $this->assertEquals($expect, $response->json()['message']);
    }

    public function testLogin()
    {
        $account = Account::find(1);

        $params = [
            'email' => $account->email,
            'password' => $account->password,
        ];

        $response = $this->call('POST', '/api/account/login', $params);

        $response->assertStatus(200);

        $result = $response->json()['result'];
        $this->assertTrue((boolean)Redis::get($result['access_token']));
        $this->assertEquals(Carbon::now()->addMinute(15)->format('Y-m-d H:i:s'), $response->json()['result']['expired_time']);
    }

    public function testRefreshToken()
    {
        $account = Account::find(1);

        $token = $this->getAccessToken($account);

        $params = [
            'access_token' => $token,
        ];

        $response = $this->call('POST', '/api/account/refreshToken', $params);

        $response->assertStatus(200);
        $this->assertNotEquals($token, $response->json()['result']['access_token']);
        $this->assertNull(Redis::get($token));
        $this->assertTrue((boolean)Redis::get($response->json()['result']['access_token']));
    }

    public function testLogout()
    {
        $account = Account::find(1);

        $token = $this->getAccessToken($account);

        $params = [
            'access_token' => $token,
        ];

        $response = $this->call('POST', '/api/account/logout', $params);

        $response->assertStatus(200);
        $this->assertNull(Redis::get($token));
    }

    public function testGetAccountInfo()
    {
        $account = Account::find(1);

        $token = $this->getAccessToken($account);

        $response = $this->withToken($token, 'Bearer')
            ->json('GET', '/api/user/me');

        $response->assertStatus(200);
        $this->assertEquals($account->toArray(), $response->json()['result']);
    }

    public function testUpdateAccountInfo()
    {
        $account = Account::find(1);

        $token = $this->getAccessToken($account);

        $params = [
            'email' => 'update@xxx.xx'
        ];

        $response = $this->withToken($token, 'Bearer')
            ->json('PATCH', '/api/user/me', $params);

        $response->assertStatus(200);
        $this->assertEquals($params['email'], $account->refresh()->email);
    }
}
