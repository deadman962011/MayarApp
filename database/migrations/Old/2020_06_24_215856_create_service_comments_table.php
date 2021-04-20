<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::create('service_comments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->String("ServiceId");
            $table->String("CustomerId");
            $table->String("CommentVoteNum");
            $table->String("CommentValue");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_comments');
    }
}
