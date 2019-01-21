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
                $table->string('model', 255);  //product,category...
                $table->string('attribute', 255);
                $table->string('attribute_type', 255);
                $table->string('default_value', 255);
                $table->boolean('single')->default(1);
                $table->boolean('with_value_map')->default(1);      //if the value is mapping to opiton_values table.
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