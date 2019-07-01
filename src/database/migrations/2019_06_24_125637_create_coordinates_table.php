<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Samanar\Map\Database\Seeds\MapTableSeeder;

class CreateCoordinatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('map.mapTableName'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('province')->index();
            $table->string('state')->index();
            $table->string('city')->index();
            $table->string('longitude');
            $table->string('latitude');
            $table->index(['province', 'state']);
        });

        MapTableSeeder::run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('map.mapTableName'));
    }
}
