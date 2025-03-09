<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDefaultSuppliers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('suppliers')->insert([
            [
                'name' => 'Farmex',
                'website' => 'https://www.farmex.com.pe/',
            ],
            [
                'name' => 'Montana',
                'website' => 'https://www.corpmontana.com/categoria/agricultura',
            ],
            [
                'name' => 'CBI',
                'website' => 'hhttps://cbiperu.com/wp/',
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
