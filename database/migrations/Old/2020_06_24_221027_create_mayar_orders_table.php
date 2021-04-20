<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMayarOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mayar_orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->String("OrderServiceId");
            $table->String("OrderUpgradesId");
            $table->String("OrderCustomerId");
            $table->String("OrderStatus");
            $table->String('OrderPayStatus');
            $table->String('OrderPrice');
            $table->String('OrderTargetId');
            $table->String('OrderFolder')->nullable();
            $table->String('OrderQr');
            $table->String('OrderQrToken');
            $table->longText('OrderDesc')->nullable();    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mayar_orders');
    }
}
