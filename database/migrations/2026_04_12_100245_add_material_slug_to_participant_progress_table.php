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
        Schema::table('participant_progress', function (Blueprint $table) {
            $table->string('material_slug')->nullable()->after('material_id');
            // Note: We can't modify the unique constraint because it's referenced by a foreign key
            // The application logic will handle the uniqueness for material_slug
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_progress', function (Blueprint $table) {
            $table->dropColumn('material_slug');
        });
    }
};
