<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_types', function(Blueprint $table){

            $table->increments('id');
            $table->string('type');

        });

        Schema::create('settings', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('setting_group_id')->unsigned()->default(1);
            $table->string('name')->unique()->index();
            $table->string('description');
            $table->integer('setting_type')->unsigned();
            $table->text('value');
            $table->integer('weight')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('setting_group_id', 'setting_group_fk')->references('id')
                ->on('setting_groups')->onDelete('cascade');

            $table->foreign('setting_type', 'setting_type_fk')->references('id')->on('setting_types');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('setting_types');
    }
}
