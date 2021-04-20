<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMayarFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mayar_files', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->String("BaseName");
            $table->String("FileName");
            $table->String("Ext");
            $table->String('FileSource');
            $table->String('StorageName');
            $table->String("OrderId")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mayar_files');
    }
}
