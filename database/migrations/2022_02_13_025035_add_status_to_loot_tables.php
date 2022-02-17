<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToLootTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loot_tables', function (Blueprint $table) {
            //
            $table->string('data', 512)->nullable()->default(null);
        });

        Schema::table('loots', function (Blueprint $table) {
            //
            $table->integer('subtable_id')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loot_tables', function (Blueprint $table) {
            //
            $table->dropColumn('data');
        });

        Schema::table('loots', function (Blueprint $table) {
            //
            $table->dropColumn('subtable_id');
        });
    }
}
