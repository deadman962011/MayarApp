<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMayarCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        Schema::create('mayar_customers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->String("CustFirstName");
            $table->String("CustLastName");
            $table->String("CustMail");
            $table->String("CustUserName");
            $table->String("CustPass");
            $table->String("CustCountry");
            $table->String("CustAddress")->nullable();
            $table->String("CustStatus");
            $table->String("CustActivationToken");
            $table->String('CustPassRestToken');
            $table->String('CustPassRestExpire');
            // $table->date('CustBirthDay')->nullable();
            // $table->longText('CustBio')->nullable();
            // $table->string('CustPic')->nullable();



        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mayar_customers');
    }
}
