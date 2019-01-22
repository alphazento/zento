<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDynamicAttributeValueMapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('dynamic_attribute_value_maps')) {
            Schema::create('dynamic_attribute_value_maps', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('attribute_id')->unsigned();
                $table->integer('value_id')->unsigned();
                $table->string('value')->nullable();
                $table->timestamps();
                // $table->foreign('attribute_set_id')
                //     ->references('id')
                //     ->on('attribute_sets')
                //     ->onDelete('cascade');
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
        Schema::drop('dynamic_attribute_value_maps');
    }
}