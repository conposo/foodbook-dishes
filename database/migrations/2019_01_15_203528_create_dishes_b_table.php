<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDishesBTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('sqlite_b')->create('dishes', function (Blueprint $table) {
            $table->increments('id');

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
        
        Schema::connection('sqlite_b')->create('tags', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedSmallInteger('dish_id');
            $table->string('tag');

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
        Schema::connection('sqlite_b')->dropIfExists('dishes');
        Schema::connection('sqlite_b')->dropIfExists('tags');
    }
}
