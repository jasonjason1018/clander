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

        $tokenInfo = $this->fakeLogin($account);

        $params = [
            'access_token' => $tokenInfo['access_token'],
        ];

        $response = $this->call('POST', '/api/account/refreshToken', $params);

        $response->assertStatus(200);
        $this->assertNotEquals($tokenInfo['access_token'], $response->json()['result']['access_token']);
        $this->assertNull(Redis::get($tokenInfo['access_token']));
        $this->assertTrue((boolean)Redis::get($response->json()['result']['access_token']));
    }

    public function testLogout()
    {
        $account = Account::find(1);

        $tokenInfo = $this->fakeLogin($account);

        $params = [
            'access_token' => $tokenInfo['access_token'],
        ];

        $response = $this->call('POST', '/api/account/logout', $params);

        $response->assertStatus(200);
        $this->assertNull(Redis::get($tokenInfo['access_token']));
    }

    public function testGetAccountInfo()
    {
        $account = Account::find(1);

        $tokenInfo = $this->fakeLogin($account);

        $response = $this->withToken($tokenInfo['access_token'], 'Bearer')
            ->json('GET', '/api/user/me');

        $response->assertStatus(200);
        $this->assertEquals($account->toArray(), $response->json()['result']);
    }

    private function fakeLogin($account)
    {
        $accountService = new AccountService();

        $tokenInfo = $accountService->getAccessToken($account);

        Redis::set($tokenInfo['access_token'], true);

        return $tokenInfo;
    }
}
