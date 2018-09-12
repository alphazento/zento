<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsBlockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_blocks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->text('content')->nullable();
            $table->string('type', 32); //page, layout, widget
            $table->string('theme', 32); //theme name
            $table->string('version', 32); //version name
            $table->boolean('hidden');  //hide from front-end
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
        Schema::drop('cms_blocks');
    }
}
