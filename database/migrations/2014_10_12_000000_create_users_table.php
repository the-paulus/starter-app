<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('auth_types')) {

            Schema::create('auth_types', function(Blueprint $table) {

                $table->increments('id');
                $table->string('name');
                $table->softDeletes();

            });

        }

        if(!Schema::hasTable('users')) {

            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->unique();
                $table->unsignedInteger('auth_type');
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
                $table->softDeletes();

                $table->index('first_name');
                $table->index('last_name');

                $table->foreign('auth_type', 'auth_type_fk')->references('id')->on('auth_types');
            });

        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('auth_types');
    }
}
