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
        Schema::table('registration_individuals', function (Blueprint $table) {
            $table->foreignId('program_id')->nullable()->constrained('programs')->onDelete('set null');
        });

        Schema::table('registration_groups', function (Blueprint $table) {
            $table->foreignId('program_id')->nullable()->constrained('programs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_individuals', function (Blueprint $table) {
            $table->dropForeignKey(['program_id']);
            $table->dropColumn('program_id');
        });

        Schema::table('registration_groups', function (Blueprint $table) {
            $table->dropForeignKey(['program_id']);
            $table->dropColumn('program_id');
        });
    }
};
