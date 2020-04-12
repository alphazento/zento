<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('package_configs')) {
            Schema::create('package_configs', function (Blueprint $table) {
                $table->increments('id');
                $table->engine = 'InnoDB';
                $table->string('name', 64)->default('');
                $table->string('version', 32)->default('0.0.1');
                $table->boolean('is_theme')->default(0);
                $table->boolean('enabled')->default(0);
                $table->string('theme')->default('');
                $table->integer('sort')->default(0);
                $table->timestamps();
                $table->unique('name');
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
        Schema::drop('package_configs');
    }
}
