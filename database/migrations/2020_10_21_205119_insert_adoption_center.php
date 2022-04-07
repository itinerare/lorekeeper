<?php

use Illuminate\Database\Migrations\Migration;

class InsertAdoptionCenter extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        //
        DB::table('adoptions')->insert(
            [
                [
                    'name'               => 'Adoption Center',
                    'has_image'          => 0,
                    'description'        => '<p>Default text</p>',
                    'parsed_description' => '<p>Default text</p>',
                    'is_active'          => 1,
                ],
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
    }
}
