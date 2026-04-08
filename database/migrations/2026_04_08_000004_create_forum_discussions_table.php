<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_discussions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name');
            $table->string('user_email')->nullable();
            $table->string('user_role');
            $table->string('title');
            $table->longText('content');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->timestamps();

            $table->index('module_id');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_discussions');
    }
};
