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
        Schema::create('participant_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->foreignId('material_id')->nullable()->constrained('module_materials')->onDelete('cascade');
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('file_size');
            $table->string('mime_type');
            $table->integer('score')->nullable(); // Score given by instructor
            $table->text('feedback')->nullable(); // Feedback from instructor
            $table->timestamp('submitted_at');
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'module_id', 'material_id'], 'unique_assignment_submission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_assignments');
    }
};
