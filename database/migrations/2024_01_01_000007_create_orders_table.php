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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->string('number')->unique();
            $table->enum('status', ['pending', 'paid', 'cancelled', 'shipped'])->default('pending');
            $table->char('currency', 3)->default('USD');
            $table->integer('subtotal');
            $table->integer('discount_total')->default(0);
            $table->integer('tax_total')->default(0);
            $table->integer('shipping_total')->default(0);
            $table->integer('grand_total');
            $table->foreignId('billing_address_id')->constrained('addresses')->onDelete('restrict');
            $table->foreignId('shipping_address_id')->constrained('addresses')->onDelete('restrict');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('number');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};

