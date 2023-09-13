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
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('order_status_id')->unsigned();
            $table->bigInteger('payment_id')->unsigned();
            $table->char('uuid', 36)->unique();
            $table->json('products')->nullable();
            $table->json('address')->nullable();
            $table->double('delivery_fee', 8, 2)->nullable();
            $table->double('amount', 8, 2);
            $table->timestamps();
            $table->timestamp('shipped_at')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('order_status_id')->references('id')->on('order_statuses');
            $table->foreign('payment_id')->references('id')->on('payments');
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
