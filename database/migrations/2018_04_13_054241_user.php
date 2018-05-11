<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class User extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_types', function(Blueprint $table) {

            $table->increments('id');
            $table->string('name');
            $table->softDeletes();

        });

        Schema::table('users', function(Blueprint $table) {

            $table->dropColumn('name');
            $table->string('first_name')->nullable(true)->after('id');
            $table->string('last_name')->nullable(true)->after('first_name');
            $table->unsignedInteger('auth_type')->after('password');
            $table->softDeletes();

            $table->index('first_name');
            $table->index('last_name');

            $table->foreign('auth_type', 'auth_type_fk')->references('id')->on('auth_types');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('auth_types');

    }
}
