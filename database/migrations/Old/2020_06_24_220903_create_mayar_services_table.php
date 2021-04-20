<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMayarServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mayar_services', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->String("ServiceName");
            $table->String("ServiceCatId");
            $table->String("ServiceDesc");
            $table->String("ServiceProviderId");
            $table->String("ServiceOrderdNum");
            $table->String("ServiceStatus");
            $table->String("ServicePrice");
            $table->String("ServiceThumb");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mayar_services');
    }
}
