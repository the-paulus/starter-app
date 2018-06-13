<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserUserGroupPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('user_user_group')) {

            Schema::create('user_user_group', function (Blueprint $table) {

                $table->integer('user_group_id')->unsigned()->index();
                $table->foreign('user_group_id')->references('id')->on('user_groups')->onDelete('cascade');
                $table->integer('user_id')->unsigned()->index();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->primary(['user_group_id', 'user_id']);

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
        Schema::drop('user_user_group');
    }
}
