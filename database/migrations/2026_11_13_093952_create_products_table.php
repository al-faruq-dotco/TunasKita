<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Relasi
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->foreignId('seller_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Informasi produk
            $table->string('name', 150);
            $table->string('slug', 180)->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->unsignedInteger('stock')->default(0);

            // Informasi tanaman
            $table->string('plant_age', 80)->nullable();
            $table->string('plant_size', 80)->nullable();

            // Status produk
            $table->enum('availability', [
                'available',
                'out_of_stock',
                'preorder'
            ])->default('available');

            $table->enum('status', [
                'active',
                'inactive'
            ])->default('active');

            $table->timestamps();

            // Index
            $table->index('name');
            $table->index(['status', 'availability']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};