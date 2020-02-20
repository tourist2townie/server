<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('guide_id')->unsigned();
            $table->foreign('guide_id')->references('id')->on('users');
            $table->integer('tourist_id')->unsigned();
            $table->foreign('tourist_id')->references('id')->on('users');
            $table->string('date');
            $table->integer('days');
            $table->string('category');
            $table->string('province');
            $table->string("status");
            $table->integer('price')->nullable();
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
        Schema::dropIfExists('packages');
    }
}
