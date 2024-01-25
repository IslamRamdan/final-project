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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            // $table->float('item_price')->nullable();
            // $table->text('item_name');

            $table->text('offer_text')->nullable();
            $table->integer('current_price')->nullable();
            $table->integer('previous_price')->nullable();
            $table->text('image_url')->nullable();
            // $table->foreign('item_id')->references('id')->on('items')->nullable();
            // $table->foreign('item_price')->references('price')->on('items')->nullable();
            // $table->foreign('item_name')->references('name')->on('items')->nullable();




            //this is for custom offers for specific customers
            $table->integer('purchase_value')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();




        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
