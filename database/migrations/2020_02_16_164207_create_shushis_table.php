<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShushisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shushis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->string('shushi_name')->nullable(true);
            $table->bigInteger('kingaku')->length(50);
            $table->date('hiduke');
            $table->string('shushi_kbn');
            $table->string('koza_id')->nullable(true);
            $table->string('category_id')->nullable(true);
            $table->string('sub_category_id')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shushis');
    }
}
