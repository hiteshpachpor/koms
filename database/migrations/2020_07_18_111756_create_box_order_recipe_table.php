<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoxOrderRecipeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('box_order_recipe', function (Blueprint $table) {
            $table->id();
            $table->integer('box_order_id');
            $table->integer('recipe_id');
            $table->string('recipe_name');
            $table->integer('ingredient_id');
            $table->string('ingredient_name');
            $table->integer('ingredient_amount');
            $table->enum(
                'ingredient_measure',
                Config::get('constants.ingredient_measure')
            );
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
        Schema::dropIfExists('box_order_recipe');
    }
}
