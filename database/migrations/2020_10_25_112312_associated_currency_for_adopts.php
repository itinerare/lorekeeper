<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AssociatedCurrencyForAdopts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
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

        Schema::table('adoption_stock', function(Blueprint $table) {
            $table->dropColumn('currency_id');
            $table->dropColumn('cost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('adoption_currency');

        Schema::table('adoption_stock', function(Blueprint $table) {
            $table->integer('currency_id')->unsigned();
            $table->integer('cost')->default(0);
        });
    }
}
