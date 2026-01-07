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
        Schema::table('products', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('products', 'stock')) {
                $table->integer('stock')->default(0)->nullable();
            }
            if (!Schema::hasColumn('products', 'original_price')) {
                $table->decimal('original_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('products', 'description')) {
                $table->longText('description')->nullable();
            }
            if (!Schema::hasColumn('products', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('products', 'sold_count')) {
                $table->integer('sold_count')->default(0);
            }
            if (!Schema::hasColumn('products', 'discussion_count')) {
                $table->integer('discussion_count')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'stock',
                'original_price',
                'description',
                'notes',
                'sold_count',
                'discussion_count',
            ]);
        });
    }
};
