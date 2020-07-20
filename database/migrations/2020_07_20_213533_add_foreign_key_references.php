<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyReferences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recipe_ingredient', function (Blueprint $table) {
            $table
                ->foreign('recipe_id')
                ->references('id')
                ->on('recipe');
            $table
                ->foreign('ingredient_id')
                ->references('id')
                ->on('ingredient');
        });

        Schema::table('box_order', function (Blueprint $table) {
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users');
            $table
                ->foreign('user_address_id')
                ->references('id')
                ->on('user_address');
        });

        Schema::table('box_order_recipe', function (Blueprint $table) {
            $table
                ->foreign('box_order_id')
                ->references('id')
                ->on('box_order');
            $table
                ->foreign('recipe_id')
                ->references('id')
                ->on('recipe');
            $table
                ->foreign('ingredient_id')
                ->references('id')
                ->on('ingredient');
        });

        Schema::table('user_address', function (Blueprint $table) {
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users');
        });
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
