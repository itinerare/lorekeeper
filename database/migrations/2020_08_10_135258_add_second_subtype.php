<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSecondSubtype extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('character_images', function(Blueprint $table) {
            $table->integer('subtype_id_2')->unsigned()->nullable()->default(null);
        });
        Schema::table('design_updates', function(Blueprint $table) {
            $table->integer('subtype_id_2')->unsigned()->nullable()->default(null);
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
        Schema::table('character_images', function(Blueprint $table) {
            $table->dropColumn('subtype_id_2');
        });
        Schema::table('design_updates', function(Blueprint $table) {
            $table->dropColumn('subtype_id_2');
        });
    }
}
