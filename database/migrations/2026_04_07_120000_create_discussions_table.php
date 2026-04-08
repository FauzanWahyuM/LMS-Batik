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
        Schema::create('discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->nullable()->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->string('user_name');
            $table->string('user_email');
            $table->string('title');
            $table->longText('content');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
            
            $table->index('module_id');
            $table->index('user_id');
            $table->index('is_pinned');
            $table->index('is_closed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussions');
    }
};
