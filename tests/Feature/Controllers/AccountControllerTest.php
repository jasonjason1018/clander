<?php

namespace Tests\Feature\Controllers;

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
        $this->freezeTime('2024-12-16 11:00:00');
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
}
