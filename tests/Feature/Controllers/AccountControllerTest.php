<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Account;

class AccountControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCreateAccount()
    {
        $params = [
            'name' => 'admin',
            'email' => 'admin@xx.x',
            'password' => 'admin',
        ];

        $response = $this->post('/api/account', $params);
        $response->assertStatus(200);

        $this->assertEquals($params['name'], $response->json()['result']['name']);
        $this->assertEquals($params['email'], $response->json()['result']['email']);

        $params = [
            'name' => 'admin2',
            'email' => 'admin@xx.x',
            'password' => 'admin2',
        ];

        $response = $this->post('/api/account', $params);
        $response->assertStatus(500);

        $expect = 'Email already in use.';
        $this->assertEquals($expect, $response->json()['message']);
    }
}
