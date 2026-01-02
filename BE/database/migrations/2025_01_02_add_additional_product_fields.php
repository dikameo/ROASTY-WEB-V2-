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
            // Add missing product fields
            if (!Schema::hasColumn('products', 'original_price')) {
                $table->decimal('original_price', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('products', 'sold_count')) {
                $table->integer('sold_count')->default(0)->after('review_count');
            }
            if (!Schema::hasColumn('products', 'discussion_count')) {
                $table->integer('discussion_count')->default(0)->after('sold_count');
            }
            if (!Schema::hasColumn('products', 'notes')) {
                $table->text('notes')->nullable()->after('image_urls');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'original_price')) {
                $table->dropColumn('original_price');
            }
            if (Schema::hasColumn('products', 'sold_count')) {
                $table->dropColumn('sold_count');
            }
            if (Schema::hasColumn('products', 'discussion_count')) {
                $table->dropColumn('discussion_count');
            }
            if (Schema::hasColumn('products', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
