<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class App\ extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_option_setting', function (Blueprint $table) {
            $table->integer('setting_option_id')->unsigned()->index();
            $table->foreign('setting_option_id')->references('id')->on('setting_options')->onDelete('cascade');
            $table->integer('setting_id')->unsigned()->index();
            $table->foreign('setting_id')->references('id')->on('settings')->onDelete('cascade');
            $table->primary(['setting_option_id', 'setting_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('setting_option_setting');
    }
}
