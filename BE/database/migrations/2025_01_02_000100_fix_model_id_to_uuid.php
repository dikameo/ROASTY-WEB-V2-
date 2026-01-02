<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix model_id to support UUID (string) instead of bigint
     */
    public function up(): void
    {
        // Fix model_has_roles table
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropIndex('model_has_roles_model_id_model_type_index');
        });

        // Change model_id from bigint to string/uuid
        DB::statement('ALTER TABLE model_has_roles ALTER COLUMN model_id TYPE varchar(36)');

        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');
        });

        // Fix model_has_permissions table
        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropIndex('model_has_permissions_model_id_model_type_index');
        });

        // Change model_id from bigint to string/uuid
        DB::statement('ALTER TABLE model_has_permissions ALTER COLUMN model_id TYPE varchar(36)');

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->index(['model_id', 'model_type'], 'model_has_permissions_model_id_model_type_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to bigint if needed
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropIndex('model_has_roles_model_id_model_type_index');
        });

        DB::statement('ALTER TABLE model_has_roles ALTER COLUMN model_id TYPE bigint USING model_id::bigint');

        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropIndex('model_has_permissions_model_id_model_type_index');
        });

        DB::statement('ALTER TABLE model_has_permissions ALTER COLUMN model_id TYPE bigint USING model_id::bigint');

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->index(['model_id', 'model_type'], 'model_has_permissions_model_id_model_type_index');
        });
    }
};
