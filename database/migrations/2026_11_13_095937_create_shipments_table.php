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
    Schema::create('shipments', function (Blueprint $table) {
        $table->id();

        $table->foreignId('order_id')
            ->constrained('orders')
            ->cascadeOnDelete();

        $table->string('recipient_name');
        $table->string('phone', 25);
        $table->text('address');

        $table->string('courier')->nullable();
        $table->string('tracking_number')->nullable();

        $table->decimal('shipping_cost', 12, 2)->default(0);

        $table->enum('status', [
            'pending',
            'processing',
            'shipped',
            'delivered',
            'cancelled'
        ])->default('pending');

        $table->timestamp('shipped_at')->nullable();
        $table->timestamp('delivered_at')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
