<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('weather', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('city');	
			$table->string('condition');	
			$table->string('description');				
			$table->string('icon');
		    $table->float('temp');
			$table->float('temp_min');	
			$table->float('temp_max');	
			$table->integer('pressure');				
			$table->integer('humidity');
		    $table->float('wind_speed');	
			$table->float('wind_deg');				
			$table->unsignedBigInteger('user_id');	
            $table->timestamps();	
        });
		Schema::table('weather', function($table) {
			$table->foreign('user_id')->references('id')->on('users');
		});	
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    Schema::table('weather', function($table) {	
        $table->dropForeign(['user_id']);
        $table->dropColumn('user_id');
    });			
        Schema::dropIfExists('weather');
    }
};
