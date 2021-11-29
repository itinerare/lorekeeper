<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusEffectTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_effects', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('name');

            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);

            $table->boolean('has_image')->default(0);

            // Different levels/severity will be stored here if set
            $table->text('data')->nullable()->default(null);
        });

        Schema::create('character_status_effects', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('status_effect_id')->unsigned();
            $table->integer('character_id')->unsigned()->index();
            $table->integer('quantity')->default(1);

            $table->timestamps();
        });

        Schema::create('status_effects_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('status_effect_id')->unsigned();
            // The sender will always be a user, and the recipient always a character
            $table->integer('sender_id')->unsigned()->nullable()->default(null);
            $table->integer('recipient_id')->unsigned()->index();

            $table->string('log', 1024)->nullable()->default(null);
            $table->string('log_type', 191);
            $table->string('data', 1024)->nullable()->default(null);

            $table->integer('quantity')->default(1);

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
        Schema::dropIfExists('status_effects');
        Schema::dropIfExists('character_status_effects');
        Schema::dropIfExists('status_effects_log');
    }
}
