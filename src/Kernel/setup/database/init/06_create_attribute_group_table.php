<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributeGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('attribute_groups')) {
            Schema::create('attribute_groups', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('attribute_group_id')->unsigned();
                $table->integer('attribute_set_id')->unsigned();
                $table->integer('sort_order')->unsigned();
                $table->string('attribute_group_name')->nullable();
                $table->string('attribute_group_code')->nullable();
                $table->string('tab_group_code')->nullable();
                $table->timestamps();
                $table->foreign('attribute_set_id')
                    ->references('id')
                    ->on('attribute_sets')
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
        Schema::drop('attribute_groups');
    }
}