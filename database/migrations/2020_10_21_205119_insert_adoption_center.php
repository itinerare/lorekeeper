<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertAdoptionCenter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::table('adoptions')->insert(
            [
                [
                    'name' => 'Adoption Center',
                    'has_image' => 0,
                    'description' => '<p>Default text</p>',
                    'parsed_description' => '<p>Default text</p>',
                    'is_active' => 1,
                ]
            ]
        );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
