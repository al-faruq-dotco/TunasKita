<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('care_guides', function (Blueprint $table) {
            $table->foreignId('guide_category_id')
                ->nullable()
                ->after('id')
                ->constrained('guide_categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('care_guides', function (Blueprint $table) {
            $table->dropForeign(['guide_category_id']);
            $table->dropColumn('guide_category_id');
        });
    }
};