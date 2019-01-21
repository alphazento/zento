<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDynamicAttributeSetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('dynamic_attribute_sets')) {
            Schema::create('dynamic_attribute_sets', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 255);
                $table->string('model', 255);  //product,category...
                $table->timestamps();
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
        Schema::drop('dynamic_attribute_sets');
    }
}