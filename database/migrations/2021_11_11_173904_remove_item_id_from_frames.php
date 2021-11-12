<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveItemIdFromFrames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('frames', function (Blueprint $table) {
            //
            $table->dropColumn('item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('frames', function (Blueprint $table) {
            //
            $table->integer('item_id')->unsigned();
        });
    }
}
