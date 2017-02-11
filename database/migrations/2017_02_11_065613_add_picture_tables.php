<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPictureTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
          $table->increments('id');
          $table->uuid('uuid')->unique();
          $table->string('title')->unique();
          $table->string('source');
          $table->string('local')->nullable();
          $table->boolean('imported')->default(0);
          $table->integer('status')->nullable();
          $table->timestamps();
        });

        Schema::create('media_meta', function(Blueprint $table) {
          $table->increments('id');
          $table->integer('media_id')->unsigned();
          $table->string('meta_key');
          $table->text('meta_value');
          $table->foreign('media_id')->references('id')->on('media');
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
        Schema::drop('media');
        Schema::drop('media_meta');
    }
}
