<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('full_name');
            $table->string('short_name');
            $table->string('cpf', 11)->unique();
            $table->string('cell_phone', 11)->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('confirmation_token', 10)->nullable();
            $table->boolean('cell_confirmed')->default(false);
            $table->string('device', 45)->nullable();
            $table->string('postal_code', 9)->nullable();
            $table->string('street')->nullable();
            $table->string('number', 45)->nullable();
            $table->string('complement', 100)->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('country')->nullable();
            $table->boolean('is_pdv_user')->default(false);
            $table->rememberToken();
            $table->softDeletes();
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
        Schema::dropIfExists('users');
    }
}
