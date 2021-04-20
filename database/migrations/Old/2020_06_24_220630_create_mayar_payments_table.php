<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMayarPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {       
        Schema::create('mayar_payments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->String("OrderId");
            $table->String("PaymentMethod");
            $table->String("PaymentValue");
            $table->String("PaymentToken")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mayar_payments');
    }
}
