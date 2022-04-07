<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSurrenderTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        //
        Schema::create('surrenders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('character_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('staff_id')->unsigned()->nullable()->default(null);
            $table->text('notes')->nullable()->default(null);
            $table->text('worth')->nullable()->default(null);
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Cancelled'])->default('Pending');
            $table->text('staff_comments')->nullable()->default(null);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        //
        Schema::dropIfExists('surrenders');
    }
}
