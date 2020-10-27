<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdoptionShop extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    Schema::create('adoptions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->boolean('has_image')->default(0);
            $table->text('description')->nullable()->default(null);
            $table->text('parsed_description')->nullable()->default(null);

            $table->boolean('is_active')->default(1);
        });
        Schema::create('adoption_stock', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('adoption_id')->unsigned()->index()->default(1);
            $table->integer('character_id')->unsigned();

            // In addition to the currency type,
            // restrict the bank you can use to buy the item - 
            // e.g. if you only want characters to be able to buy the item,
            // turn off use_user_bank so it forces the user to enter a character to buy it
            // Of course this requires a sanity check to make sure that
            // the currency type matches up, but that's on the game designer
            $table->boolean('use_user_bank')->default(1);
            $table->boolean('use_character_bank')->default(1);

            $table->boolean('is_limited_stock')->default(1);
            $table->integer('quantity')->default(1);
            
            $table->integer('sort')->unsigned()->default(0);
        });
        Schema::create('adoption_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('adoption_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->integer('character_id')->unsigned()->nullable()->default(null);
            
            $table->integer('currency_id')->unsigned();
            $table->integer('cost')->default(0);

            $table->integer('adopt_id')->unsigned();
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
        Schema::dropIfExists('adoption_log');
        Schema::dropIfExists('adoption_stock');
        Schema::dropIfExists('adoptions');
    }
}
