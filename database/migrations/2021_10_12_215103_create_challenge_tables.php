<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChallengeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('name', 191);

            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);
			$table->text('rules')->nullable()->default(null);
            $table->boolean('is_active')->default(1);

            $table->text('data')->nullable()->default(null);
        });

        Schema::create('user_challenges', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('user_id')->unsigned()->index();
            $table->integer('staff_id')->unsigned()->nullable()->default(null);
            $table->integer('challenge_id')->unsigned();

			$table->enum('status', ['Active', 'Old'])->default('Active');
            $table->text('staff_comments')->nullable()->default(null);

            $table->text('data')->nullable()->default(null);

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
        Schema::dropIfExists('challenges');
        Schema::dropIfExists('user_challenges');
    }
}
