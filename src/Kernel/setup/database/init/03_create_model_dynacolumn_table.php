<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelDynacolumnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('model_dynacolumns')) {
            Schema::create('model_dynacolumns', function (Blueprint $table) {
                $table->increments('id');
                $table->string('dynacolumn', 255);
                $table->string('model', 255);  //product,category...
                $table->string('col_type', 255);
                $table->string('default_value', 255);
                $table->boolean('single')->default(1);
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
        Schema::drop('model_dynacolumns');
    }
}