<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDynacolumnSetDynacolumnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('dynacolumn_set_dynacolumns')) {
            Schema::create('dynacolumn_set_dynacolumns', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('dynacolumn_set_id')->unsigned();
                $table->integer('dynacolumn_id')->unsigned();
                $table->timestamps();
                $table->foreign('dynacolumn_set_id')
                    ->references('id')
                    ->on('dynacolumn_sets')
                    ->onDelete('cascade');
                $table->foreign('dynacolumn_id')
                    ->references('id')
                    ->on('model_dynacolumns')
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
        Schema::drop('dynacolumn_set_dynacolumns');
    }
}