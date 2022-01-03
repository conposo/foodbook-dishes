<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDishesPTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('sqlite_p')->create('dishes', function (Blueprint $table) {
            $table->increments('id');

            // $table->unsignedTinyInteger('public');
            $table->boolean('public');
            $table->string('owner_type', 1);
            $table->unsignedSmallInteger('owner_id')->nullable();

            $table->string('slug', 256);
            $table->string('en_name', 256)->nullable();
            $table->string('bg_name', 256);

            $table->string('description');
            
            $table->string('category', 32);
            
            $table->unsignedSmallInteger('recipe_id')->nullable();

            $table->timestamps();
        });
        
        Schema::connection('sqlite_p')->create('tags', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedSmallInteger('dish_id');
            $table->string('tag');

            $table->timestamps();
        });

        Schema::connection('sqlite_p')->create('categories', function (Blueprint $table) {
            $table->increments('id');
            
            $table->unsignedInteger('owner_id');

            $table->string('category', 128);
            $table->string('en_name', 128);
            $table->string('bg_name', 128);
            
            $table->unsignedTinyInteger('order')->nullable();

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
        Schema::dropIfExists('dishes');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('categories');
    }
}
