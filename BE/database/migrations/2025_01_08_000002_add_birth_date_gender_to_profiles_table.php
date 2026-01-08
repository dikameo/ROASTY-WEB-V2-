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
        Schema::table('profiles', function (Blueprint $table) {
            // Add birth_date and gender columns if they don't exist
            if (!Schema::hasColumn('profiles', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('profiles', 'gender')) {
                $table->enum('gender', ['male', 'female'])->nullable()->after('birth_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            // Drop columns if they exist
            if (Schema::hasColumn('profiles', 'birth_date')) {
                $table->dropColumn('birth_date');
            }
            if (Schema::hasColumn('profiles', 'gender')) {
                $table->dropColumn('gender');
            }
        });
    }
};
