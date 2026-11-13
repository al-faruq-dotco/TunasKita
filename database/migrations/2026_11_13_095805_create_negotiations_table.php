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
    Schema::create('negotiations', function (Blueprint $table) {
        $table->id();

        $table->foreignId('order_id')
            ->nullable()
            ->constrained('orders')
            ->nullOnDelete();

        $table->foreignId('customer_id')
            ->constrained('users')
            ->restrictOnDelete();

        $table->foreignId('seller_id')
            ->constrained('users')
            ->restrictOnDelete();

        $table->decimal('offered_price', 12, 2);
        $table->decimal('proposed_price', 12, 2)->nullable();

        $table->text('message')->nullable();

        $table->enum('status', [
            'pending',
            'accepted',
            'rejected',
            'cancelled'
        ])->default('pending');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('negotiations');
    }
};
