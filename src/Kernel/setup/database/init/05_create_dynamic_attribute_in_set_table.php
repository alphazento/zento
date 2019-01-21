<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDynamicAttributeInSetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('dynamic_attribute_in_sets')) {
            Schema::create('dynamic_attribute_in_sets', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('attribute_set_id')->unsigned();
                $table->integer('attribute_id')->unsigned();
                $table->timestamps();
                $table->foreign('attribute_set_id')
                    ->references('id')
                    ->on('attribute_sets')
                    ->onDelete('cascade');
                $table->foreign('attribute_id')
                    ->references('id')
                    ->on('model_dynamic_attributes')
                    ->onDelete('cascade');
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
        Schema::drop('dynamic_attribute_in_sets');
    }
}