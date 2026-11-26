<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $uniqueKeys = [
            'carts' => ['user_id', 'carts_user_id_unique'],
            'payments' => ['order_id', 'payments_order_id_unique'],
            'shipments' => ['order_id', 'shipments_order_id_unique'],
        ];

        foreach ($uniqueKeys as $table => [$column]) {
            $hasDuplicates = DB::table($table)
                ->select($column)
                ->groupBy($column)
                ->havingRaw('COUNT(*) > 1')
                ->exists();

            if ($hasDuplicates) {
                throw new RuntimeException("Cannot add unique constraint: duplicate values exist in {$table}.{$column}.");
            }
        }

        foreach ($uniqueKeys as $table => [$column, $indexName]) {
            Schema::table($table, function (Blueprint $blueprint) use ($column, $indexName): void {
                $blueprint->unique($column, $indexName);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table): void {
            $table->dropUnique('carts_user_id_unique');
        });
        Schema::table('payments', function (Blueprint $table): void {
            $table->dropUnique('payments_order_id_unique');
        });
        Schema::table('shipments', function (Blueprint $table): void {
            $table->dropUnique('shipments_order_id_unique');
        });
    }
};
