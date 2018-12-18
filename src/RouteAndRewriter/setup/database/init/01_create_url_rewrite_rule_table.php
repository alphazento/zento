<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUrlRewriteRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('url_rewrite_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->unsigned();
            $table->string('req_hash', 32)->index();
            $table->text('req_uri');
            $table->text('to_uri');
            $table->text('params'); // json
            $table->boolean('is_system')->default(1);  //customize or system generated
            $table->string('route');   //when is_system =1, will use this route
            $table->smallInteger('status_code')->unsigned()->default(200);
            $table->string('description', 255)->nullable();
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
        Schema::drop('url_rewrite_rules');
    }
}