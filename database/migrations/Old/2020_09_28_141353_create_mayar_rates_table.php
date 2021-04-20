<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMayarRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        //MayarRateCustId  MayarRateValue MayarRateBody
        Schema::create('mayar_rates', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->String('MayarRateCustId');
            $table->Integer('MayarRateValue');
            $table->String('MayarRateBody');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mayar_rates');
    }
}
