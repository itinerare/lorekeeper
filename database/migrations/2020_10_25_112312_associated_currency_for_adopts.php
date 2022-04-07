<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssociatedCurrencyForAdopts extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        //
        Schema::create('adoption_currency', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('stock_id')->unsigned();
            $table->integer('currency_id')->unsigned();
            $table->integer('cost')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        //
        Schema::dropIfExists('adoption_currency');
    }
}
