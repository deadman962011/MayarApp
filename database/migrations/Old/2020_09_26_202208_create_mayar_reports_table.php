<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMayarReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        //     
        Schema::create('mayar_reports', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->String('ReportTotalOrders');
            $table->String('ReportPaidOrders');
            $table->String('ReportCanceldOrders');
            $table->String('ReportType');
            $table->String('ReportIncome');
            $table->String('ReportDate');
            $table->String('ReportFile');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mayar_reports');
    }
}
