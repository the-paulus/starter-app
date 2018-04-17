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
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('setting_group_id')->unsigned()->nullable();
            $table->string('name')->unique()->index();
            $table->string('description');
            $table->enum('type', ['integer','ip','ip4','ip6','email','date','string']);
            $table->text('value');
            $table->integer('weight')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('setting_group_id', 'setting_group_fk')->references('id')
                ->on('setting_groups')->onDelete('cascade');
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
    }
}
