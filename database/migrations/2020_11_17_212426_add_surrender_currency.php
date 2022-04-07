<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSurrenderCurrency extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        //
        Schema::table('surrenders', function (Blueprint $table) {
            $table->integer('currency_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        //
        schema::table('surrenders', function (Blueprint $table) {
            $table->dropColumn('currency_id');
        });
    }
}
