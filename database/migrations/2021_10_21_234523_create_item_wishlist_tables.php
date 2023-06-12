<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemWishlistTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_wishlists', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('user_id')->unsigned()->index();
            $table->string('name')->nullable()->default(null);
        });

        Schema::create('user_wishlist_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('wishlist_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->nullable()->default(null);
            $table->integer('item_id')->unsigned();

            $table->integer('count')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_wishlists');
        Schema::dropIfExists('user_wishlist_items');
    }
}
