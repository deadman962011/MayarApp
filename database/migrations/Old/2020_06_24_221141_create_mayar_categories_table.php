<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMayarCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mayar_categories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->String("CategoryName");
            $table->String("CategoryServiceNum");
            $table->String('CategoryThumb');
            $table->String('CategoryCover');
            $table->text('CategoryDesc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mayar_categories');
    }
}
