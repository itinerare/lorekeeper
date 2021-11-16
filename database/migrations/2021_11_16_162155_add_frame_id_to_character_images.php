<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFrameIdToCharacterImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('character_images', function (Blueprint $table) {
            //
            $table->integer('frame_id')->unsigned()->nullable()->default(null);
        });

        Schema::table('design_updates', function (Blueprint $table) {
            //
            $table->integer('frame_id')->unsigned()->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('character_images', function (Blueprint $table) {
            //
            $table->dropColumn('frame_id');
        });

        Schema::table('design_updates', function (Blueprint $table) {
            //
            $table->dropColumn('frame_id');
        });
    }
}
