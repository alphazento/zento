<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

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
                $table->string('value')->nullable();
                $table->string('value_in_container')->nullable();
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
        Schema::drop('dynamic_attribute_value_maps');
    }
}
