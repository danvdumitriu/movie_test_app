<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopratedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('toprateds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('top_number');
            $table->integer('movie_id')->unsigned()->unique();
            $table->foreign('movie_id')->references('id')->on('movies');
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
        Schema::dropIfExists('toprateds');
    }
}
