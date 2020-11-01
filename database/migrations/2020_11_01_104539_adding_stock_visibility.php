<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingStockVisibility extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('adoption_stock', function(Blueprint $table) {
            $table->boolean('is_visible')->default(1);
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
        Schema::table('adoption_stock', function(Blueprint $table) {
            $table->dropColumn('is_visible');
        });
    }
}
