<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_detail', function (Blueprint $table) {
            $table->bigIncrements('id_news_detial');            
            $table->bigInteger('id_user');
            $table->bigInteger('id_news');
            $table->text('content');
            $table->timestamps();
            $table->softDeletes();     
           });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news_detail');
    }
}
