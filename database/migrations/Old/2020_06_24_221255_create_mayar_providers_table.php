<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMayarProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mayar_providers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->String('ProviderUserName');
            $table->String('ProviderPass');
            $table->String('ProviderServiceNum');
            $table->String('ProviderIconSrc');
            $table->String('ProviderDesc');
            $table->String('ProviderName');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mayar_providers');
    }
}
