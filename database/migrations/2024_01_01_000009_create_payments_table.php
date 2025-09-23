<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('provider')->default('stripe');
            $table->string('reference');
            $table->integer('amount');
            $table->enum('status', ['requires_payment', 'processing', 'paid', 'failed', 'refunded'])->default('requires_payment');
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index('reference');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
