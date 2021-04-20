<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMayarAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mayar_ads', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->String('AdName');
            $table->String('AdImg');
            $table->String('AdValue');
            $table->String('AdStatus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mayar_ads');
    }
}
