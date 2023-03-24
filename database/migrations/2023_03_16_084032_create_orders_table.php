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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('user_address_id');
            $table->string('order_num');
            $table->enum('status', ['pending', 'accept', 'reject', 'out of delivery', 'delivered'])->default('pending');
            $table->char('expected_delivery_date');


            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_address_id')->references('id')->on('user_addresses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
