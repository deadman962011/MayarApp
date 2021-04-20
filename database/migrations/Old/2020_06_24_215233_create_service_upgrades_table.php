<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceUpgradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_upgrades', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->String("ServiceId");
            $table->String("UpgradeTitle");
            $table->String("UpgradeDesc");
            $table->String("UpgradePrice");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_upgrades');
    }
}
