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
        Schema::table('user_addresses', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('user_addresses', 'label')) {
                $table->string('label')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('user_addresses', 'recipient_name')) {
                $table->string('recipient_name')->nullable()->after('label');
            }
            if (!Schema::hasColumn('user_addresses', 'phone')) {
                $table->string('phone')->nullable()->after('recipient_name');
            }
            if (!Schema::hasColumn('user_addresses', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('user_addresses', 'province')) {
                $table->string('province')->nullable()->after('address');
            }
            if (!Schema::hasColumn('user_addresses', 'city')) {
                $table->string('city')->nullable()->after('province');
            }
            if (!Schema::hasColumn('user_addresses', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('city');
            }
            if (!Schema::hasColumn('user_addresses', 'is_primary')) {
                $table->boolean('is_primary')->default(false)->after('postal_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            if (Schema::hasColumn('user_addresses', 'label')) $table->dropColumn('label');
            if (Schema::hasColumn('user_addresses', 'recipient_name')) $table->dropColumn('recipient_name');
            if (Schema::hasColumn('user_addresses', 'phone')) $table->dropColumn('phone');
            if (Schema::hasColumn('user_addresses', 'address')) $table->dropColumn('address');
            if (Schema::hasColumn('user_addresses', 'province')) $table->dropColumn('province');
            if (Schema::hasColumn('user_addresses', 'city')) $table->dropColumn('city');
            if (Schema::hasColumn('user_addresses', 'postal_code')) $table->dropColumn('postal_code');
            if (Schema::hasColumn('user_addresses', 'is_primary')) $table->dropColumn('is_primary');
        });
    }
};
