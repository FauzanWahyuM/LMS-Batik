<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('duration_hours', 8, 2);
            $table->decimal('fee_amount', 14, 2);
            $table->string('fee_unit', 50)->default('per peserta');
            $table->text('description');
            $table->json('benefits')->default(json_encode([]));
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
