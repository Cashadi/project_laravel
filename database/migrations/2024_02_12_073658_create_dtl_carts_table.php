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
        Schema::create('dtl_carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hdr_carts_id');
            $table->unsignedBigInteger('products_id');
            $table->integer('quantity');

            $table->foreign('hdr_carts_id')->references('id')->on('hdr_carts');
            $table->foreign('products_id')->references('id')->on('products');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dtl_carts');
    }
};
