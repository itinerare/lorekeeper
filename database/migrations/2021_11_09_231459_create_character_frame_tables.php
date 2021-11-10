<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharacterFrameTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('frame_categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('name');
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);

            $table->boolean('has_image')->default(0);
            $table->integer('sort')->default(0);
        });

        // While most interaction with the frame system will be via items,
        // it's still useful to store frames as their own objects for data storage
        Schema::create('frames', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('frame_category_id')->unsigned()->nullable()->default(null);

            $table->string('name');
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
            $table->string('hash', 15);

            // This will record the matching item for the frame, for ease of access
            $table->integer('item_id')->unsigned();

            $table->boolean('is_default')->default(0);
        });

        // Frame unlocks for characters are lightweight; only the character and frame
        // need to be recorded. It's useful to have timestamps as well, though
        Schema::create('character_frames', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('character_id')->unsigned()->index();
            $table->integer('frame_id')->unsigned();

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
        Schema::dropIfExists('frame_categories');
        Schema::dropIfExists('frames');
        Schema::dropIfExists('character_frames');
    }
}
