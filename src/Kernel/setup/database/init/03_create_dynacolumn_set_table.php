<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDynacolumnSetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dynacolumn_sets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('model', 255);  //product,category...
            // $table->text('dynacolumns')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dynacolumn_sets');
    }
}