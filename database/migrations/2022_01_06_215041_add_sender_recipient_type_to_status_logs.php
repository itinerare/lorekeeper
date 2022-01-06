<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSenderRecipientTypeToStatusLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('status_effects_log', function (Blueprint $table) {
            //
            $table->enum('sender_type', ['User', 'Character'])->nullable()->default(null);
            $table->enum('recipient_type', ['User', 'Character'])->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('status_effects_log', function (Blueprint $table) {
            //
            $table->dropColumn('sender_type');
            $table->dropColumn('recipient_type');
        });
    }
}
