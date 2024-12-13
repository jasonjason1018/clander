<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id('id_account');
            $table->string('name', 50);
            $table->string('email', 50)
                ->unique();
            $table->string('password', 1000);
            $table->tinyInteger('status')
                ->default(1)
                ->comment('0 => deactivate, 1 => active');
            $table->string('note', 100)
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
