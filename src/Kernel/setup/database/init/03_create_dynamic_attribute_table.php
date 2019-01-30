<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDynamicAttributeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('dynamic_attributes')) {
            Schema::create('dynamic_attributes', function (Blueprint $table) {
                $table->increments('id');
                $table->string('parent_table', 255);  //product,category...
                $table->string('attribute_name', 255);
                $table->string('label', 255);
                $table->string('attribute_table', 255);
                $table->string('attribute_type', 255);
                $table->string('default_value', 255)->nullable();
                $table->boolean('single')->default(1);
                $table->boolean('with_value_map')->default(1);      //if the value is mapping to opiton_values table.
                $table->smallInteger('swatch_type')->default(0);    //0 means not a swatch attribute
                $table->boolean('is_search_layer')->default(0);
                $table->smallInteger('search_layer_sort')->default(0);
                $table->boolean('enabled')->default(1);
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
        Schema::drop('dynamic_attributes');
    }
}