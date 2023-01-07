<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LatLongTagsSinusvalue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sinusvalues', function (Blueprint $table) {    
            $table->double('latitude', 11, 8)->nullable(); 
            $table->double('longitude', 11, 8)->nullable();
            $table->string('tags', 254);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sinusvalues', function (Blueprint $table) {    
            $table->dropColumn('latitude'); 
            $table->dropColumn('longitude');
            $table->dropColumn('tags');
        });
    }
}
