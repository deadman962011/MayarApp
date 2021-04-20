<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMayarNotifsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('mayar_notifs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->String("NotifStatus");
            $table->String("NotifValue");
            $table->String("NotifTargetType");
            $table->String("NotifTargetId");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mayar_notifs');
    }
}
