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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->decimal('total', 8, 2);
            $table->string('order_id')->nullable();
            $table->string('stripe_cus_id', 1024)->nullable();
            $table->string('stripe_sub_id', 1024)->nullable();
            $table->string('stripe_payment_intent_id', 1024)->nullable();
            $table->string('stripe_payment_method', 1024)->nullable();
            $table->string('stripe_payment_status', 1024)->nullable();
            $table->bigInteger('date')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
