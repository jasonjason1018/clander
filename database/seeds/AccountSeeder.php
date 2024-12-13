<?php

use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Account::create([
            'name' => 'admin2',
            'email' => 'admin@xx.x',
            'password' => 'admin',
        ]);
    }
}
